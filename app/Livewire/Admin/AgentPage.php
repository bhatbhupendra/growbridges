<?php

namespace App\Livewire\Admin;

use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class AgentPage extends Component
{
    public User $agent;

    #[Url]
    public string $intake = 'all';

    #[Url(as: 'school_id')]
    public string $schoolId = 'all';

    #[Url]
    public string $nationality = 'all';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $pipeline = 'all';

    public array $selectedApplications = [];
    public array $schoolStatusInputs = [];

    public ?int $assignStudentId = null;
    public ?int $assignSchoolId = null;
    public string $assignStudentName = '';
    public array $assignAvailableSchools = [];

    public string $editName = '';
    public string $editEmail = '';
    public string $editPassword = '';

    private array $allowedStatuses = [
        'interview',
        'selected',
        'rejected',
        'coe-applied',
        'coe-granted',
        'coe-rejected',
        'visa-granted',
        'visa-rejected',
        'withdrawal',
    ];

    public function mount(User $agent): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        $this->agent = $agent;

        $this->editName = (string) $agent->name;
        $this->editEmail = (string) $agent->email;
    }

   public function resetFilters(): void
    {
        $this->intake = 'all';
        $this->schoolId = 'all';
        $this->nationality = 'all';
        $this->status = 'all';
        $this->pipeline = 'all';
    }

    public function setPipeline(string $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    public function saveSchoolStatus(int $applicationId): void
    {
        $status = $this->schoolStatusInputs[$applicationId] ?? null;

        if (! $status || ! in_array($status, $this->allowedStatuses, true)) {
            session()->flash('error', 'Invalid status selected.');
            return;
        }

        $application = StudentSchoolApplication::with('student')->findOrFail($applicationId);

        if ((int) optional($application->student)->created_by !== (int) $this->agent->id) {
            abort(404);
        }

        $application->update([
            'status' => $status,
        ]);

        $this->schoolStatusInputs[$applicationId] = $application->status;

        session()->flash('success', 'Application status updated successfully.');
    }

    public function openAssignSchoolModal(int $studentId): void
    {
        $student = Student::with('applications.school')
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->findOrFail($studentId);

        $assignedSchoolIds = $student->applications
            ->pluck('school_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->assignStudentId = $student->id;
        $this->assignSchoolId = null;
        $this->assignStudentName = (string) ($student->student_name ?? 'Student');

        $this->assignAvailableSchools = School::query()
            ->whereNotIn('id', $assignedSchoolIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($school) => [
                'id' => $school->id,
                'name' => $school->name,
            ])
            ->values()
            ->toArray();

        $this->dispatch('open-assign-school-modal');
    }

    public function assignSchool(): void
    {
        $this->validate([
            'assignStudentId' => ['required', 'integer', 'exists:students,id'],
            'assignSchoolId' => ['required', 'integer', 'exists:schools,id'],
        ]);

        $student = Student::query()
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->findOrFail($this->assignStudentId);

        $newApp = StudentSchoolApplication::firstOrCreate(
            [
                'student_id' => $student->id,
                'school_id' => (int) $this->assignSchoolId,
            ],
            [
                'status' => 'pending',
                'assigned_by' => auth()->id(),
                'applied_by' => auth()->id(),
                'applied_at' => now(),
            ]
        );

        if (! $newApp->wasRecentlyCreated) {
            session()->flash('error', 'This student is already assigned to that school.');
            return;
        }

        $this->dispatch('close-assign-school-modal');
        session()->flash('success', 'Student assigned to another school successfully.');
    }

    public function removeAssignedSchoolDirect(int $studentId, int $targetApplicationId, int $defaultApplicationId): void
    {
        $student = Student::query()
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->findOrFail($studentId);

        $targetApplication = StudentSchoolApplication::with('school')->findOrFail($targetApplicationId);

        if ((int) $targetApplication->student_id !== (int) $student->id) {
            session()->flash('error', 'Selected school assignment does not belong to this student.');
            return;
        }

        if ((int) $targetApplication->id === (int) $defaultApplicationId) {
            session()->flash('error', 'You cannot remove the current school from this row.');
            return;
        }

        $targetSchoolName = $targetApplication->school?->name ?? 'selected school';

        $targetApplication->delete();

        unset($this->schoolStatusInputs[$targetApplicationId]);

        if (($this->selectedApplications[$studentId] ?? null) == $targetApplicationId) {
            unset($this->selectedApplications[$studentId]);
        }

        session()->flash('success', "Student removed from {$targetSchoolName} successfully.");
    }

    public function updateAgent(): void
    {
        $validated = $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editEmail' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->agent->id),
            ],
            'editPassword' => ['nullable', 'string', 'min:8'],
        ], [], [
            'editName' => 'name',
            'editEmail' => 'email',
            'editPassword' => 'password',
        ]);

        $data = [
            'name' => trim($validated['editName']),
            'email' => trim($validated['editEmail']),
        ];

        if (! empty($validated['editPassword'])) {
            $data['password'] = Hash::make($validated['editPassword']);
        }

        $this->agent->update($data);
        $this->agent->refresh();

        $this->editName = (string) $this->agent->name;
        $this->editEmail = (string) $this->agent->email;
        $this->editPassword = '';

        $this->dispatch('close-edit-user-modal');
        session()->flash('success', 'Agent updated successfully!');
    }

    public function deleteAgent(): mixed
    {
        $totalStudents = Student::query()
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->count();

        if ($totalStudents > 0) {
            session()->flash('error', 'Cannot delete: this agent has students. Delete or move students first.');
            return null;
        }

        $this->agent->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Agent deleted successfully.');
    }

    public function deleteStudent(int $studentId): void
    {
        $student = Student::query()
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->findOrFail($studentId);

        $student->delete();

        unset($this->selectedApplications[$studentId]);

        session()->flash('success', 'Student moved to recycle bin successfully.');
    }

    public function render()
    {
        $intakes = Student::query()
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->whereNotNull('intake')
            ->where('intake', '<>', '')
            ->distinct()
            ->orderByDesc('intake')
            ->pluck('intake');

        $nationalities = Student::query()
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->whereNotNull('nationality')
            ->where('nationality', '<>', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        $schools = School::query()
            ->whereIn('id', function ($q) {
                $q->select('school_id')
                    ->from('student_school_applications')
                    ->whereIn('student_id', function ($sq) {
                        $sq->select('id')
                            ->from('students')
                            ->where('created_by', $this->agent->id)
                            ->whereNull('deleted_at');
                    });
            })
            ->orderBy('name')
            ->get();

        $allSchools = School::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $studentsQuery = Student::query()
            ->with(['applications.school', 'applications', 'creator'])
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->latest('id');

        if ($this->intake !== 'all' && $this->intake !== '') {
            $studentsQuery->where('intake', $this->intake);
        }

        if ($this->nationality !== 'all' && $this->nationality !== '') {
            $studentsQuery->where('nationality', $this->nationality);
        }

        if ($this->schoolId !== 'all' && ctype_digit((string) $this->schoolId)) {
            $schoolId = (int) $this->schoolId;

            $studentsQuery->whereHas('applications', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        if ($this->status !== 'all' && in_array($this->status, $this->allowedStatuses, true)) {
            $studentsQuery->whereHas('applications', function ($q) {
                $q->where('status', $this->status);
            });
        }

        $students = $studentsQuery->get();

        $studentRows = $students->map(function ($student) use ($allSchools) {
            $applications = $student->applications
                ->filter(fn ($app) => $app->school)
                ->values();

            $submittedDocTypeIds = StudentDocument::query()
                ->where('student_id', $student->id)
                ->pluck('doc_type_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $submittedDocTypeIds = array_flip($submittedDocTypeIds);

            $schoolsData = $applications->map(function ($app) use ($submittedDocTypeIds, $student) {
                $school = $app->school;

                $docOutput = collect();

                $requiredDocs = SchoolRequiredDoc::with('documentType')
                    ->where('school_id', $school->id)
                    ->get();

                foreach ($requiredDocs as $req) {
                    $dt = $req->documentType;

                    if (! $dt) {
                        continue;
                    }

                    $submitted = isset($submittedDocTypeIds[(int) $dt->id]);

                    $docOutput->push([
                        'name' => $dt->doc_name,
                        'submitted' => $submitted,
                    ]);
                }

                return [
                    'id' => $school->id,
                    'application_id' => $app->id,
                    'name' => $school->name,
                    'status' => $app->status ?? 'pending',
                    'docs' => $docOutput->values()->all(),
                    'view_url' => route('student.file.show', [$student, $school]),
                ];
            })->values();

            $defaultSchool = null;

            if ($this->schoolId !== 'all' && ctype_digit((string) $this->schoolId)) {
                $defaultSchool = $schoolsData->firstWhere('id', (int) $this->schoolId);
            }

            if (! $defaultSchool) {
                $defaultSchool = $schoolsData->first();
            }

            $defaultApplicationId = $defaultSchool['application_id'] ?? null;

            if (
                ! isset($this->selectedApplications[$student->id]) ||
                ! $applications->firstWhere('id', (int) $this->selectedApplications[$student->id])
            ) {
                $this->selectedApplications[$student->id] = $defaultApplicationId;
            }

            $selectedApplicationId = $this->selectedApplications[$student->id] ?? $defaultApplicationId;

            $activeSchool = $schoolsData->firstWhere('application_id', (int) $selectedApplicationId)
                ?? $defaultSchool;

            $assignedSchoolIds = $applications
                ->pluck('school_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $assignedSchools = $applications
                ->map(function ($app) use ($defaultApplicationId) {
                    return [
                        'application_id' => $app->id,
                        'school_id' => $app->school_id,
                        'school_name' => $app->school->name,
                        'status' => $app->status ?? 'pending',
                        'is_current' => (int) $app->id === (int) $defaultApplicationId,
                    ];
                })
                ->values();

            foreach ($assignedSchools as $assignedSchool) {
                $assignedApplicationId = (int) $assignedSchool['application_id'];

                if (! isset($this->schoolStatusInputs[$assignedApplicationId])) {
                    $this->schoolStatusInputs[$assignedApplicationId] = $assignedSchool['status'] ?? 'pending';
                }
            }

            $availableSchools = $allSchools
                ->reject(fn ($s) => in_array((int) $s->id, $assignedSchoolIds, true))
                ->values();

            $rawPhotoPath = trim((string) ($student->photo ?? ''));
            $rawPhotoPath = str_replace('\\', '/', $rawPhotoPath);
            $rawPhotoPath = preg_replace('#^/?storage/#', '', $rawPhotoPath);
            $rawPhotoPath = ltrim($rawPhotoPath, '/');
            $photoUrl = $rawPhotoPath !== '' ? asset('storage/' . $rawPhotoPath) : null;

            return [
                'student' => $student,
                'schools' => $schoolsData,
                'active_school' => $activeSchool,
                'selected_application_id' => $selectedApplicationId,
                'default_application_id' => $defaultApplicationId,
                'assigned_schools' => $assignedSchools,
                'available_schools' => $availableSchools,
                'photo_url' => $photoUrl,
                'pipeline_stage' => $this->getPipelineStage($student),
            ];
        })->values();

        $counts = [
            'all' => $studentRows->count(),
            'new' => $studentRows->where('pipeline_stage', 'new')->count(),
            'incomplete' => $studentRows->where('pipeline_stage', 'incomplete')->count(),
            'incomplete_language' => $studentRows->where('pipeline_stage', 'incomplete_language')->count(),
            'ready' => $studentRows->where('pipeline_stage', 'ready')->count(),
            'assigned' => $studentRows->where('pipeline_stage', 'assigned')->count(),
            'interview' => $studentRows->where('pipeline_stage', 'interview')->count(),
            'selected' => $studentRows->where('pipeline_stage', 'selected')->count(),
            'rejected_all' => $studentRows->where('pipeline_stage', 'rejected_all')->count(),
        ];

        $filteredRows = $studentRows;

        if ($this->pipeline !== 'all') {
            $filteredRows = $filteredRows
                ->filter(fn ($row) => $row['pipeline_stage'] === $this->pipeline)
                ->values();
        }

        return view('livewire.admin.agent-page', [
            'agent' => $this->agent,
            'students' => $filteredRows,
            'intakes' => $intakes,
            'schools' => $schools,
            'nationalities' => $nationalities,
            'selectedIntake' => $this->intake,
            'selectedSchool' => $this->schoolId,
            'selectedNationality' => $this->nationality,
            'selectedStatus' => $this->status,
            'allowedStatuses' => $this->allowedStatuses,
            'counts' => $counts,
        ]);
    }

    private function getPipelineStage(Student $student): string
    {
        $realApplications = $student->applications->where('school_id', '!=', 1);

        if ($realApplications->isEmpty()) {
            return $student->pre_school_status ?: 'new';
        }

        $statuses = $realApplications
            ->pluck('status')
            ->filter()
            ->map(fn ($status) => strtolower((string) $status))
            ->values();

        if ($statuses->contains(fn ($status) => in_array($status, ['selected', 'coe-applied', 'coe-granted', 'visa-granted'], true))) {
            return 'selected';
        }

        if ($statuses->contains('interview')) {
            return 'interview';
        }

        if (
            $realApplications->count() > 0 &&
            $statuses->count() === $realApplications->count() &&
            $statuses->every(fn ($status) => in_array($status, ['rejected', 'coe-rejected', 'visa-rejected', 'withdrawal'], true))
        ) {
            return 'rejected_all';
        }

        return 'assigned';
    }
}