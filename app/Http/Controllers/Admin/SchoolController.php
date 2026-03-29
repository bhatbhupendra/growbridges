<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolController extends Controller
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

    public function show(Request $request, School $school): View
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $selectedIntake = trim((string) $request->query('intake', 'all'));
        $selectedNationality = trim((string) $request->query('nationality', 'all'));
        $selectedAgent = trim((string) $request->query('agent_id', 'all'));
        $selectedStatus = trim((string) $request->query('status', 'all'));

        $studentCount = StudentSchoolApplication::query()
            ->where('school_id', $school->id)
            ->count();

        $user = User::query()
            ->where('school_id', $school->id)
            ->first();

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

        $agents = User::query()
            ->where('role', 'agent')
            ->whereIn('id', function ($q) use ($school) {
                $q->select('students.created_by')
                    ->from('students')
                    ->join('student_school_applications', 'student_school_applications.student_id', '=', 'students.id')
                    ->where('student_school_applications.school_id', $school->id)
                    ->whereNull('students.deleted_at')
                    ->whereNotNull('students.created_by');
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $allSchools = School::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $applicationsQuery = StudentSchoolApplication::query()
            ->with(['student.applications.school', 'student.creator', 'school'])
            ->where('school_id', $school->id)
            ->whereHas('student', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->latest();

        if ($selectedIntake !== 'all' && $selectedIntake !== '') {
            $applicationsQuery->whereHas('student', function ($q) use ($selectedIntake) {
                $q->where('intake', $selectedIntake);
            });
        }

        if ($selectedNationality !== 'all' && $selectedNationality !== '') {
            $applicationsQuery->whereHas('student', function ($q) use ($selectedNationality) {
                $q->where('nationality', $selectedNationality);
            });
        }

        if ($selectedAgent !== 'all' && ctype_digit((string) $selectedAgent)) {
            $agentId = (int) $selectedAgent;

            $applicationsQuery->whereHas('student', function ($q) use ($agentId) {
                $q->where('created_by', $agentId);
            });
        }

        if ($selectedStatus !== 'all' && in_array($selectedStatus, $this->allowedStatuses, true)) {
            $applicationsQuery->where('status', $selectedStatus);
        }

        $applications = $applicationsQuery->get();

        $requiredDocs = SchoolRequiredDoc::query()
            ->with('documentType')
            ->where('school_id', $school->id)
            ->get()
            ->filter(fn ($req) => $req->documentType);

        $studentIds = $applications->pluck('student_id')->unique()->values();

        $submittedDocs = StudentDocument::query()
            ->whereIn('student_id', $studentIds)
            ->where('school_id', $school->id)
            ->get(['student_id', 'doc_type_id'])
            ->groupBy('student_id')
            ->map(function ($docs) {
                return $docs->pluck('doc_type_id')->unique()->toArray();
            });

        $rows = $applications->map(function ($application) use ($school, $requiredDocs, $submittedDocs, $allSchools) {
            $student = $application->student;
            $studentSubmittedDocIds = $submittedDocs->get($student->id, []);

            $docOutput = $requiredDocs->map(function ($req) use ($studentSubmittedDocIds) {
                $dt = $req->documentType;

                return [
                    'name' => $dt->doc_name,
                    'submitted' => in_array($dt->id, $studentSubmittedDocIds),
                ];
            });

            $assignedSchoolIds = $student->applications
                ->pluck('school_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $availableSchools = $allSchools
                ->reject(fn ($s) => in_array((int) $s->id, $assignedSchoolIds, true))
                ->values();

            return [
                'application' => $application,
                'student' => $student,
                'school' => $school,
                'docs' => $docOutput,
                'available_schools' => $availableSchools,
            ];
        });

        return view('admin.school.show', [
            'school' => $school,
            'studentCount' => $studentCount,
            'user' => $user,
            'rows' => $rows,
            'intakes' => $intakes,
            'nationalities' => $nationalities,
            'agents' => $agents,
            'selectedIntake' => $selectedIntake,
            'selectedNationality' => $selectedNationality,
            'selectedAgent' => $selectedAgent,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $school->update([
            'name' => trim($request->name),
        ]);

        return redirect()
            ->route('admin.school.show', $school)
            ->with('success', 'School name updated successfully!');
    }

    public function destroy(School $school): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $studentCount = StudentSchoolApplication::query()
            ->where('school_id', $school->id)
            ->count();

        if ($studentCount > 0) {
            return redirect()
                ->route('admin.school.show', $school)
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
}