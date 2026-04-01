<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AgentController extends Controller
{
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

    public function show(Request $request, User $agent): View
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        $selectedIntake = trim((string) $request->query('intake', 'all'));
        $selectedSchool = trim((string) $request->query('school_id', 'all'));
        $selectedNationality = trim((string) $request->query('nationality', 'all'));
        $selectedStatus = trim((string) $request->query('status', 'all'));

        $intakes = Student::query()
            ->where('created_by', $agent->id)
            ->whereNull('deleted_at')
            ->whereNotNull('intake')
            ->where('intake', '<>', '')
            ->distinct()
            ->orderByDesc('intake')
            ->pluck('intake');

        $nationalities = Student::query()
            ->where('created_by', $agent->id)
            ->whereNull('deleted_at')
            ->whereNotNull('nationality')
            ->where('nationality', '<>', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        $schools = School::query()
            ->whereIn('id', function ($q) use ($agent) {
                $q->select('school_id')
                    ->from('student_school_applications')
                    ->whereIn('student_id', function ($sq) use ($agent) {
                        $sq->select('id')
                            ->from('students')
                            ->where('created_by', $agent->id)
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
            ->where('created_by', $agent->id)
            ->whereNull('deleted_at')
            ->latest('id');

        if ($selectedIntake !== 'all' && $selectedIntake !== '') {
            $studentsQuery->where('intake', $selectedIntake);
        }

        if ($selectedNationality !== 'all' && $selectedNationality !== '') {
            $studentsQuery->where('nationality', $selectedNationality);
        }

        if ($selectedSchool !== 'all' && ctype_digit((string) $selectedSchool)) {
            $schoolId = (int) $selectedSchool;

            $studentsQuery->whereHas('applications', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        if ($selectedStatus !== 'all' && in_array($selectedStatus, $this->allowedStatuses, true)) {
            $studentsQuery->whereHas('applications', function ($q) use ($selectedStatus) {
                $q->where('status', $selectedStatus);
            });
        }

        $students = $studentsQuery->get();

        $studentRows = $students->map(function ($student) use ($selectedSchool, $allSchools) {
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
                    'application_id' => $app->id,
                    'name' => $school->name,
                    'status' => $app->status ?? 'pending',
                    'docs' => $docOutput->values()->all(),
                    'view_url' => route('student.file.show', [$student, $school]),
                ];
            })->values();

            $defaultSchool = null;

            if ($selectedSchool !== 'all' && ctype_digit((string) $selectedSchool)) {
                $defaultSchool = $schoolsData->firstWhere('id', (int) $selectedSchool);
            }

            if (!$defaultSchool) {
                $defaultSchool = $schoolsData->first();
            }

            $assignedSchoolIds = $applications
                ->pluck('school_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $assignedSchools = $applications
                ->map(function ($app) use ($defaultSchool) {
                    return [
                        'application_id' => $app->id,
                        'school_id' => $app->school_id,
                        'school_name' => $app->school->name,
                        'status' => $app->status ?? 'pending',
                        'is_current' => $defaultSchool ? ((int) $app->id === (int) $defaultSchool['application_id']) : false,
                    ];
                })
                ->values();

            $availableSchools = $allSchools
                ->reject(fn ($s) => in_array((int) $s->id, $assignedSchoolIds, true))
                ->values();

            return [
                'student' => $student,
                'schools' => $schoolsData,
                'active_school_id' => $defaultSchool['id'] ?? null,
                'active_application_id' => $defaultSchool['application_id'] ?? null,
                'assigned_schools' => $assignedSchools,
                'available_schools' => $availableSchools,
            ];
        });

        return view('admin.agents.show', [
            'agent' => $agent,
            'students' => $studentRows,
            'intakes' => $intakes,
            'schools' => $schools,
            'nationalities' => $nationalities,
            'selectedIntake' => $selectedIntake,
            'selectedSchool' => $selectedSchool,
            'selectedNationality' => $selectedNationality,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function assignStudentToSchool(Request $request, User $agent, Student $student): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        if ((int) $student->created_by !== (int) $agent->id) {
            abort(404);
        }

        $data = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
        ]);

        $newApp = StudentSchoolApplication::firstOrCreate(
            [
                'student_id' => $student->id,
                'school_id' => (int) $data['school_id'],
            ],
            [
                'status' => 'pending',
                'assigned_by' => auth()->id(),
                'applied_by' => auth()->id(),
                'applied_at' => now(),
            ]
        );

        if (!$newApp->wasRecentlyCreated) {
            return redirect()
                ->route('admin.agents.show', $agent)
                ->with('error', 'This student is already assigned to that school.');
        }

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Student assigned to another school successfully.');
    }

    public function removeStudentFromSchool(
        Request $request,
        User $agent,
        Student $student,
        StudentSchoolApplication $targetApplication
    ): RedirectResponse {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        if ((int) $student->created_by !== (int) $agent->id) {
            abort(404);
        }

        if ((int) $targetApplication->student_id !== (int) $student->id) {
            return redirect()
                ->route('admin.agents.show', $agent)
                ->with('error', 'Selected school assignment does not belong to this student.');
        }

        $currentApplicationId = $request->input('current_application_id');

        if ($currentApplicationId && (int) $targetApplication->id === (int) $currentApplicationId) {
            return redirect()
                ->route('admin.agents.show', $agent)
                ->with('error', 'You cannot remove the current school from this row.');
        }

        $targetSchoolName = $targetApplication->school?->name ?? 'selected school';

        $targetApplication->delete();

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', "Student removed from {$targetSchoolName} successfully.");
    }

    public function updateStatus(Request $request, User $agent, StudentSchoolApplication $application): RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        if ((int) optional($application->student)->created_by !== (int) $agent->id) {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', $this->allowedStatuses)],
        ]);

        $application->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Application status updated successfully.');
    }

    public function update(UpdateAgentRequest $request, User $agent): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        $data = [
            'name' => trim($request->name),
            'email' => trim($request->email),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $agent->update($data);

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Agent updated successfully!');
    }

    public function destroy(User $agent): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        $totalStudents = Student::where('created_by', $agent->id)
            ->whereNull('deleted_at')
            ->count();

        if ($totalStudents > 0) {
            return redirect()
                ->route('admin.agents.show', $agent)
                ->with('error', 'Cannot delete: this agent has students. Delete or move students first.');
        }

        $agent->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Agent deleted successfully.');
    }
}