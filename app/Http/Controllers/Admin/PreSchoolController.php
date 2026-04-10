<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PreSchoolController extends Controller
{
    private array $allowedStatuses = [
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

    private array $allowedPreSchoolStatuses = [
        'new',
        'incomplete',
        'incomplete_language',
        'ready',
    ];

    public function show(Request $request, School $school): View
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $selectedIntake       = trim((string) $request->query('intake', 'all'));
        $selectedAgent        = trim((string) $request->query('agent_id', 'all'));
        $selectedStatus       = trim((string) $request->query('status', 'all'));
        $selectedNationality  = trim((string) $request->query('nationality', 'all'));
        $selectedPipeline     = trim((string) $request->query('pipeline', 'all'));
        $search               = trim((string) $request->query('search', ''));

        $studentCount = StudentSchoolApplication::where('school_id', $school->id)->count();
        $user = User::where('school_id', $school->id)->first();

        $intakes = StudentSchoolApplication::query()
            ->join('students', 'students.id', '=', 'student_school_applications.student_id')
            ->where('student_school_applications.school_id', $school->id)
            ->whereNull('students.deleted_at')
            ->whereNotNull('students.intake')
            ->where('students.intake', '<>', '')
            ->distinct()
            ->orderByDesc('students.intake')
            ->pluck('students.intake');

        $nationalities = StudentSchoolApplication::query()
            ->join('students', 'students.id', '=', 'student_school_applications.student_id')
            ->where('student_school_applications.school_id', $school->id)
            ->whereNull('students.deleted_at')
            ->whereNotNull('students.nationality')
            ->where('students.nationality', '<>', '')
            ->distinct()
            ->orderBy('students.nationality')
            ->pluck('students.nationality');

        $agents = StudentSchoolApplication::query()
            ->join('students', 'students.id', '=', 'student_school_applications.student_id')
            ->join('users', 'users.id', '=', 'students.created_by')
            ->where('student_school_applications.school_id', $school->id)
            ->whereNull('students.deleted_at')
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        $allSchools = School::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $baseQuery = StudentSchoolApplication::query()
            ->with([
                'school',
                'student.creator',
                'student.applications.school',
            ])
            ->where('school_id', $school->id)
            ->whereHas('student', function ($q) {
                $q->whereNull('deleted_at');
            });

        if ($selectedIntake !== 'all' && $selectedIntake !== '') {
            $baseQuery->whereHas('student', function ($q) use ($selectedIntake) {
                $q->where('intake', $selectedIntake);
            });
        }

        if ($selectedNationality !== 'all' && $selectedNationality !== '') {
            $baseQuery->whereHas('student', function ($q) use ($selectedNationality) {
                $q->where('nationality', $selectedNationality);
            });
        }

        if ($selectedAgent !== 'all' && ctype_digit((string) $selectedAgent)) {
            $agentId = (int) $selectedAgent;

            $baseQuery->whereHas('student', function ($q) use ($agentId) {
                $q->where('created_by', $agentId);
            });
        }

        if ($selectedStatus !== 'all' && in_array($selectedStatus, $this->allowedStatuses, true)) {
            $baseQuery->where('status', $selectedStatus);
        }

        if ($search !== '') {
            $baseQuery->whereHas('student', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('student_name', 'like', "%{$search}%")
                        ->orWhere('student_name_jp', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('intake', 'like', "%{$search}%");
                });
            });
        }

        $applications = $baseQuery->latest()->get();

        $rows = $applications->map(function ($application) use ($allSchools, $school) {
            $student = $application->student;
            $currentSchool = $application->school;

            $docOutput = collect();
            if ($currentSchool) {
                $requiredDocs = SchoolRequiredDoc::with('documentType')
                    ->where('school_id', $currentSchool->id)
                    ->get();

                foreach ($requiredDocs as $req) {
                    $dt = $req->documentType;
                    if (!$dt) {
                        continue;
                    }

                    $submitted = StudentDocument::query()
                        ->where('student_id', $student->id)
                        ->where('doc_type_id', $dt->id)
                        ->exists();

                    $docOutput->push([
                        'name' => $dt->doc_name,
                        'submitted' => $submitted,
                    ]);
                }
            }

            $assignedSchoolIds = $student->applications
                ->pluck('school_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $assignedSchools = $student->applications
                ->filter(fn ($app) => $app->school)
                ->map(function ($app) use ($application) {
                    return [
                        'application_id' => $app->id,
                        'school_id'      => $app->school_id,
                        'school_name'    => $app->school->name,
                        'status'         => $app->status ?? 'pending',
                        'is_current'     => (int) $app->id === (int) $application->id,
                    ];
                })
                ->values();

            $availableSchools = $allSchools
                ->reject(fn ($s) => in_array((int) $s->id, $assignedSchoolIds, true))
                ->values();

            $photoUrl = null;
            if (!empty($student->photo)) {
                $photoUrl = asset('storage/' . ltrim($student->photo, '/'));
            } else {
                $photoDocument = StudentDocument::query()
                    ->where('student_id', $student->id)
                    ->whereHas('documentType', function ($q) {
                        $q->whereIn('file_type', ['jpg', 'jpeg']);
                    })
                    ->latest()
                    ->first();

                $photoUrl = $photoDocument ? Storage::url($photoDocument->file_path) : null;
            }

            $profileMeta = $this->buildProfileMeta($student);

            return [
                'application'                  => $application,
                'student'                      => $student,
                'school'                       => $currentSchool,
                'docs'                         => $docOutput,
                'photo_url'                    => $photoUrl,
                'available_schools'            => $availableSchools,
                'assigned_schools'             => $assignedSchools,
                'pipeline_stage'               => $this->getPipelineStage($student),
                'profile_completion_percent'   => $profileMeta['completion_percent'],
                'missing_profile_fields'       => $profileMeta['missing_fields'],
                'assigned_real_school_count'   => $student->applications->where('school_id', '!=', 1)->count(),
            ];
        })->values();

        $counts = [
            'all'          => $rows->count(),
            'new'          => $rows->where('pipeline_stage', 'new')->count(),
            'incomplete'   => $rows->where('pipeline_stage', 'incomplete')->count(),
            'incomplete_language' => $rows->where('pipeline_stage', 'incomplete_language')->count(),
            'ready'        => $rows->where('pipeline_stage', 'ready')->count(),
            'assigned'     => $rows->where('pipeline_stage', 'assigned')->count(),
            'interview'    => $rows->where('pipeline_stage', 'interview')->count(),
            'selected'     => $rows->where('pipeline_stage', 'selected')->count(),
            'rejected_all' => $rows->where('pipeline_stage', 'rejected_all')->count(),
        ];

        if ($selectedPipeline !== 'all') {
            $rows = $rows->filter(function ($row) use ($selectedPipeline) {
                return $row['pipeline_stage'] === $selectedPipeline;
            })->values();
        }

        return view('admin.preschool.show', [
            'school'               => $school,
            'studentCount'         => $studentCount,
            'user'                 => $user,
            'intakes'              => $intakes,
            'nationalities'        => $nationalities,
            'agents'               => $agents,
            'applications'         => $applications,
            'rows'                 => $rows,
            'counts'               => $counts,
            'selectedNationality'  => $selectedNationality,
            'selectedIntake'       => $selectedIntake,
            'selectedAgent'        => $selectedAgent,
            'selectedStatus'       => $selectedStatus,
            'selectedPipeline'     => $selectedPipeline,
            'search'               => $search,
        ]);
    }

    public function assignStudentToSchool(Request $request, School $school, StudentSchoolApplication $application): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        if ((int) $application->school_id !== (int) $school->id) {
            abort(404);
        }

        $data = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
        ]);

        $student = $application->student;

        $newApp = StudentSchoolApplication::firstOrCreate(
            [
                'student_id' => $student->id,
                'school_id'  => (int) $data['school_id'],
            ],
            [
                'status'      => 'pending',
                'assigned_by' => auth()->id(),
                'applied_by'  => auth()->id(),
                'applied_at'  => now(),
            ]
        );

        if (!$newApp->wasRecentlyCreated) {
            return redirect()
                ->route('admin.preschool.show', $school)
                ->with('error', 'This student is already assigned to that school.');
        }

        return redirect()
            ->route('admin.preschool.show', $school)
            ->with('success', 'Student assigned to another school successfully.');
    }

    public function removeStudentFromSchool(
        Request $request,
        School $school,
        StudentSchoolApplication $application,
        StudentSchoolApplication $targetApplication
    ): RedirectResponse {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        if ((int) $application->school_id !== (int) $school->id) {
            abort(404);
        }

        if ((int) $application->student_id !== (int) $targetApplication->student_id) {
            return redirect()
                ->route('admin.preschool.show', $school)
                ->with('error', 'Selected school assignment does not belong to this student.');
        }

        if ((int) $targetApplication->id === (int) $application->id) {
            return redirect()
                ->route('admin.preschool.show', $school)
                ->with('error', 'You cannot remove the current school from this row.');
        }

        $targetSchoolName = $targetApplication->school?->name ?? 'selected school';

        $targetApplication->delete();

        return redirect()
            ->route('admin.preschool.show', $school)
            ->with('success', "Student removed from {$targetSchoolName} successfully.");
    }

    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $school->update([
            'name' => trim($request->name),
        ]);

        return redirect()
            ->route('admin.preschool.show', $school)
            ->with('success', 'School name updated successfully!');
    }

    public function destroy(School $school): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $studentCount = StudentSchoolApplication::where('school_id', $school->id)->count();

        if ($studentCount > 0) {
            return redirect()
                ->route('admin.preschool.show', $school)
                ->with('error', 'Cannot delete this school because students are enrolled. Remove or move them first.');
        }

        DB::transaction(function () use ($school) {
            SchoolRequiredDoc::where('school_id', $school->id)->delete();
            $school->delete();
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'School deleted successfully!');
    }

    public function updateStatus(Request $request, StudentSchoolApplication $application): RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', $this->allowedStatuses)],
        ]);

        $application->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Application status updated successfully.');
    }

    public function updateReview(Request $request, StudentSchoolApplication $application): RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $data = $request->validate([
            'pre_school_status'   => ['required', 'in:' . implode(',', $this->allowedPreSchoolStatuses)],
            'admin_review_notes'  => ['nullable', 'string'],
        ]);

        $student = $application->student;

        $student->update([
            'pre_school_status'  => $data['pre_school_status'],
            'admin_review_notes' => $data['admin_review_notes'] ?? null,
            'admin_reviewed_at'  => now(),
        ]);

        return back()->with('success', 'Pre-school review updated successfully.');
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

        if ($statuses->contains(fn ($status) => in_array($status, ['selected', 'coe-applied', 'coe-granted', 'visa-applied', 'visa-granted'], true))) {
            return 'selected';
        }

        if ($statuses->contains('interview')) {
            return 'interview';
        }

        if ($realApplications->count() > 0 && $statuses->count() === $realApplications->count() && $statuses->every(fn ($status) => in_array($status, ['rejected', 'coe-rejected', 'visa-rejected', 'withdrawal'], true))) {
            return 'rejected_all';
        }

        return 'assigned';
    }

    private function buildProfileMeta(Student $student): array
    {
        $fields = [
            'Student name'        => !empty($student->student_name),
            'Student name JP'     => !empty($student->student_name_jp),
            'DOB'                 => !empty($student->dob),
            'Gender'              => !empty($student->gender),
            'Nationality'         => !empty($student->nationality),
            'Phone'               => !empty($student->phone),
            'Passport number'     => !empty($student->passport_number),
            'Current address'     => !empty($student->current_address),
            'Permanent address'   => !empty($student->permanent_address),
            'Photo'               => !empty($student->photo),
            'Father name'         => !empty($student->father_name),
            'Father occupation'   => !empty($student->father_occupation),
            'Mother name'         => !empty($student->mother_name),
            'Mother occupation'   => !empty($student->mother_occupation),
            'Marital status'      => !empty($student->marital_status),
        ];

        $total = count($fields);
        $done = collect($fields)->filter()->count();

        return [
            'completion_percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
            'missing_fields'     => collect($fields)
                ->filter(fn ($ok) => !$ok)
                ->keys()
                ->values()
                ->toArray(),
        ];
    }
}