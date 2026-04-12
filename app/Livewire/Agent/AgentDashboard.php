<?php

namespace App\Livewire\Agent;

use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class AgentDashboard extends Component
{
    public $agent;

    #[Url]
    public string $intake = 'all';

    #[Url(as: 'school_id')]
    public string $schoolId = 'all';

    #[Url]
    public string $pipeline = 'all';

    public array $selectedSchools = [];

    public string $editName = '';
    public string $editEmail = '';
    public string $editPassword = '';

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'agent', 403);

        $this->agent = auth()->user();

        $this->editName = (string) $this->agent->name;
        $this->editEmail = (string) $this->agent->email;
    }

    public function resetFilters(): void
    {
        $this->intake = 'all';
        $this->schoolId = 'all';
        $this->pipeline = 'all';
    }

    public function setPipeline(string $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    public function updateProfile(): void
    {
        $validated = $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editEmail' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->agent->id],
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

        if (!empty($validated['editPassword'])) {
            $data['password'] = Hash::make($validated['editPassword']);
        }

        $this->agent->update($data);
        $this->agent->refresh();

        $this->editName = (string) $this->agent->name;
        $this->editEmail = (string) $this->agent->email;
        $this->editPassword = '';

        $this->dispatch('close-edit-profile-modal');
        session()->flash('success', 'Profile updated successfully!');
    }

    public function deleteStudent(int $studentId): void
    {
        $student = Student::query()
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->findOrFail($studentId);

        $student->delete();

        unset($this->selectedSchools[$studentId]);

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

        $studentsQuery = Student::query()
            ->with(['applications.school','strength'])
            ->where('created_by', $this->agent->id)
            ->whereNull('deleted_at')
            ->latest('id');

        if ($this->intake !== 'all' && $this->intake !== '') {
            $studentsQuery->where('intake', $this->intake);
        }

        if ($this->schoolId !== 'all' && ctype_digit((string) $this->schoolId)) {
            $schoolId = (int) $this->schoolId;

            $studentsQuery->whereHas('applications', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        $students = $studentsQuery->get();

        $studentRows = $students->map(function ($student) {
            $applications = $student->applications
                ->filter(fn ($app) => $app->school)
                ->values();

            $submittedDocTypeIds = StudentDocument::query()
                ->where('student_id', $student->id)
                ->pluck('doc_type_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $submittedDocTypeIds = array_flip($submittedDocTypeIds);

            $schoolsData = $applications->map(function ($app) use ($student, $submittedDocTypeIds) {
                $school = $app->school;

                $docOutput = collect();

                $requiredDocs = SchoolRequiredDoc::with('documentType')
                    ->where('school_id', $school->id)
                    ->get();

                foreach ($requiredDocs as $req) {
                    $dt = $req->documentType;

                    if (!$dt) {
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

            if (!$defaultSchool) {
                $defaultSchool = $schoolsData->first();
            }

            $defaultSchoolId = $defaultSchool['id'] ?? null;

            if (
                !isset($this->selectedSchools[$student->id]) ||
                !$schoolsData->firstWhere('id', (int) $this->selectedSchools[$student->id])
            ) {
                $this->selectedSchools[$student->id] = $defaultSchoolId;
            }

            $selectedSchoolId = $this->selectedSchools[$student->id] ?? $defaultSchoolId;

            $activeSchool = $schoolsData->firstWhere('id', (int) $selectedSchoolId) ?? $defaultSchool;

            $rawPhotoPath = trim((string) ($student->photo ?? ''));
            $rawPhotoPath = str_replace('\\', '/', $rawPhotoPath);
            $rawPhotoPath = preg_replace('#^/?storage/#', '', $rawPhotoPath);
            $rawPhotoPath = ltrim($rawPhotoPath, '/');
            $photoUrl = $rawPhotoPath !== '' ? asset('storage/' . $rawPhotoPath) : null;

            return [
                'student' => $student,
                'schools' => $schoolsData,
                'active_school_id' => $selectedSchoolId,
                'active_school' => $activeSchool,
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

        return view('livewire.agent.agent-dashboard', [
            'agent' => $this->agent,
            'students' => $filteredRows,
            'intakes' => $intakes,
            'schools' => $schools,
            'selectedIntake' => $this->intake,
            'selectedSchool' => $this->schoolId,
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