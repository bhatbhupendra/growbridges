<?php

namespace App\Livewire\Inspector;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use App\Models\StudentStrength;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class InspectorDashboard extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $intake = 'all';

    #[Url(as: 'agent_id')]
    public string $agentId = 'all';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $nationality = 'all';

    #[Url]
    public string $schoolFilter = 'all';

    #[Url]
    public string $pipeline = 'all';

    public array $reviewInputs = [];
    public array $schoolStatusInputs = [];
    public array $strengthInputs = [];

    public array $allowedStatuses = [
        'pending',
        'interview',
        'selected',
        'rejected',
        'coe-applied',
        'coe-granted',
        'coe-rejected',
        'visa-applied',
        'visa-granted',
        'visa-rejected',
        'withdrawal',
    ];

    public array $allowedPreSchoolStatuses = [
        'new',
        'incomplete',
        'incomplete_language',
        'ready',
    ];

    public function mount(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'inspector', 403);
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

        $application = StudentSchoolApplication::whereHas('student.inspectors', function ($q) {
                $q->where('users.id', auth()->id());
            })
            ->findOrFail($applicationId);

        $application->update([
            'status' => $status,
        ]);

        session()->flash('success', 'Application status updated successfully.');
    }

    public function saveReview(int $applicationId): void
    {
        $input = $this->reviewInputs[$applicationId] ?? null;

        if (! $input) {
            return;
        }

        $preSchoolStatus = $input['pre_school_status'] ?? 'new';
        $adminReviewNotes = $input['admin_review_notes'] ?? null;

        if (! in_array($preSchoolStatus, $this->allowedPreSchoolStatuses, true)) {
            return;
        }

        $application = StudentSchoolApplication::with('student')
            ->whereHas('student.inspectors', function ($q) {
                $q->where('users.id', auth()->id());
            })
            ->findOrFail($applicationId);

        $application->student->update([
            'pre_school_status'  => $preSchoolStatus,
            'admin_review_notes' => filled($adminReviewNotes) ? $adminReviewNotes : null,
            'admin_reviewed_at'  => now(),
        ]);

        session()->flash('success', 'Review updated successfully.');
    }

    public function saveStrength(int $studentId): void
    {
        $student = Student::whereHas('inspectors', function ($q) {
                $q->where('users.id', auth()->id());
            })
            ->findOrFail($studentId);

        $input = $this->strengthInputs[$student->id] ?? [];

        $hiragana = max(0, min(100, (int) ($input['hiragana'] ?? 0)));
        $katagana = max(0, min(100, (int) ($input['katagana'] ?? 0)));
        $numbers = max(0, min(100, (int) ($input['numbers'] ?? 0)));
        $interview = max(0, min(100, (int) ($input['interview'] ?? 0)));

        $overall = round(($hiragana + $katagana + $numbers + $interview) / 4);

        StudentStrength::updateOrCreate(
            ['student_id' => $student->id],
            [
                'hiragana' => $hiragana,
                'katagana' => $katagana,
                'numbers' => $numbers,
                'interview' => $interview,
                'overall' => $overall,
            ]
        );

        $this->strengthInputs[$student->id] = [
            'hiragana' => $hiragana,
            'katagana' => $katagana,
            'numbers' => $numbers,
            'interview' => $interview,
            'overall' => $overall,
        ];

        session()->flash('success', 'Language strength saved successfully.');
    }

    public function openAssignModal(int $applicationId): void
    {
        session()->flash('error', 'Inspector cannot assign students to schools.');
    }

    public function assignSchool(): void
    {
        session()->flash('error', 'Inspector cannot assign students to schools.');
    }

    public function removeAssignedSchoolDirect(int $currentApplicationId, int $targetApplicationId): void
    {
        session()->flash('error', 'Inspector cannot remove school assignments.');
    }

    public function render()
    {
        $inspectorId = auth()->id();

        $studentCount = Student::whereHas('inspectors', function ($q) use ($inspectorId) {
            $q->where('users.id', $inspectorId);
        })->count();

        $user = Auth::user();

        $intakes = Student::query()
            ->whereHas('inspectors', function ($q) use ($inspectorId) {
                $q->where('users.id', $inspectorId);
            })
            ->whereNull('deleted_at')
            ->whereNotNull('intake')
            ->where('intake', '<>', '')
            ->distinct()
            ->orderByDesc('intake')
            ->pluck('intake');

        $nationalities = Student::query()
            ->whereHas('inspectors', function ($q) use ($inspectorId) {
                $q->where('users.id', $inspectorId);
            })
            ->whereNull('deleted_at')
            ->whereNotNull('nationality')
            ->where('nationality', '<>', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        $agents = User::query()
            ->whereIn('id', function ($q) use ($inspectorId) {
                $q->select('students.created_by')
                    ->from('students')
                    ->join('student_inspector_assignments', 'student_inspector_assignments.student_id', '=', 'students.id')
                    ->where('student_inspector_assignments.inspector_id', $inspectorId)
                    ->whereNull('students.deleted_at');
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $allSchools = School::orderBy('name')->get(['id', 'name']);

        $baseQuery = StudentSchoolApplication::query()
            ->with([
                'student.creator',
                'student.applications.school',
                'student.strength',
                'school',
            ])
            ->whereHas('student.inspectors', function ($q) use ($inspectorId) {
                $q->where('users.id', $inspectorId);
            })
            ->whereHas('student', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->when($this->search !== '', function ($q) {
                $search = trim($this->search);

                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('student_name', 'like', "%{$search}%")
                        ->orWhere('student_name_jp', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%");
                });
            })
            ->when($this->intake !== 'all', function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('intake', $this->intake);
                });
            })
            ->when($this->nationality !== 'all', function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('nationality', $this->nationality);
                });
            })
            ->when($this->agentId !== 'all', function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('created_by', $this->agentId);
                });
            })
            ->when($this->schoolFilter !== 'all', function ($q) {
                $q->where('school_id', $this->schoolFilter);
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
            });

        $applications = $baseQuery
            ->latest()
            ->get()
            ->unique('student_id')
            ->values();

        $rows = $applications->map(function ($application) {
            $student = $application->student;

            $assignedSchools = $student->applications
                ->map(function ($app) {
                    return [
                        'application_id' => $app->id,
                        'school_id' => $app->school_id,
                        'school_name' => $app->school?->name ?? '-',
                        'status' => $app->status ?? 'pending',
                    ];
                })
                ->values()
                ->toArray();

            $stage = $this->getPipelineStage($student, $assignedSchools);

            $this->reviewInputs[$application->id] = [
                'pre_school_status' => $student->pre_school_status ?? 'new',
                'admin_review_notes' => $student->admin_review_notes ?? '',
            ];

            foreach ($student->applications as $app) {
                $this->schoolStatusInputs[$app->id] = $app->status ?? 'pending';
            }

            $strength = $student->strength;

            $this->strengthInputs[$student->id] = [
                'hiragana' => $strength->hiragana ?? 0,
                'katagana' => $strength->katagana ?? 0,
                'numbers' => $strength->numbers ?? 0,
                'interview' => $strength->interview ?? 0,
                'overall' => $strength->overall ?? 0,
            ];

            return [
                'application' => $application,
                'student' => $student,
                'assigned_schools' => $assignedSchools,
                'assigned_real_school_count' => count($assignedSchools),
                'pipeline_stage' => $stage,
                'profile_completion_percent' => $this->profileCompletionPercent($student),
                'missing_profile_fields' => $this->missingProfileFields($student),
                'photo_url' => $this->getStudentPhotoUrl($student->id),
            ];
        });

        if ($this->pipeline !== 'all') {
            $rows = $rows->where('pipeline_stage', $this->pipeline)->values();
        }

        $counts = [
            'all' => $rows->count(),
            'new' => $rows->where('pipeline_stage', 'new')->count(),
            'incomplete' => $rows->where('pipeline_stage', 'incomplete')->count(),
            'incomplete_language' => $rows->where('pipeline_stage', 'incomplete_language')->count(),
            'ready' => $rows->where('pipeline_stage', 'ready')->count(),
            'assigned' => $rows->where('pipeline_stage', 'assigned')->count(),
            'interview' => $rows->where('pipeline_stage', 'interview')->count(),
            'selected' => $rows->where('pipeline_stage', 'selected')->count(),
            'rejected_all' => $rows->where('pipeline_stage', 'rejected_all')->count(),
        ];

        return view('livewire.inspector.inspector-dashboard', [
            'rows' => $rows,
            'counts' => $counts,
            'studentCount' => $studentCount,
            'user' => $user,
            'intakes' => $intakes,
            'nationalities' => $nationalities,
            'agents' => $agents,
            'allSchools' => $allSchools,
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }

    private function getPipelineStage($student, array $assignedSchools): string
    {
        $preStatus = $student->pre_school_status ?? 'new';

        if (in_array($preStatus, ['new', 'incomplete', 'incomplete_language', 'ready'], true)) {
            return $preStatus;
        }

        $statuses = collect($assignedSchools)->pluck('status')->map(fn ($s) => strtolower($s));

        if ($statuses->contains('selected')) {
            return 'selected';
        }

        if ($statuses->contains('interview')) {
            return 'interview';
        }

        if ($statuses->count() > 0 && $statuses->every(fn ($s) => $s === 'rejected')) {
            return 'rejected_all';
        }

        if ($statuses->count() > 0) {
            return 'assigned';
        }

        return 'new';
    }

    private function missingProfileFields($student): array
    {
        $missing = [];

        $fields = [
            'student_name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'nationality' => 'Nationality',
            'gender' => 'Gender',
            'passport_number' => 'Passport',
            'intake' => 'Intake',
        ];

        foreach ($fields as $field => $label) {
            if (blank($student->{$field} ?? null)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    private function profileCompletionPercent($student): int
    {
        $fields = [
            'student_name',
            'email',
            'phone',
            'nationality',
            'gender',
            'passport_number',
            'intake',
        ];

        $filled = 0;

        foreach ($fields as $field) {
            if (filled($student->{$field} ?? null)) {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    private function getStudentPhotoUrl(int $studentId): ?string
    {
        $doc = StudentDocument::where('student_id', $studentId)
            ->where(function ($q) {
                $q->where('file_path', 'like', '%.jpg')
                    ->orWhere('file_path', 'like', '%.jpeg')
                    ->orWhere('file_path', 'like', '%.png');
            })
            ->latest()
            ->first();

        if (! $doc || ! $doc->file_path) {
            return null;
        }

        $path = str_replace('\\', '/', $doc->file_path);
        $path = preg_replace('/^storage\//', '', $path);

        return asset('storage/' . $path);
    }
}