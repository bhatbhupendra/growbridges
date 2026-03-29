<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolRequiredDoc;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SchoolDashboardController extends Controller
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

    public function index(Request $request): View
    {
        abort_unless(Auth::check() && Auth::user()->role === 'school', 403);

        $user = Auth::user();
        abort_unless($user->school_id, 403);

        $school = $user->school;

        $selectedIntake = trim((string) $request->query('intake', 'all'));
        $selectedAgent = trim((string) $request->query('agent_id', 'all'));
        $selectedStatus = trim((string) $request->query('status', 'all'));
        $selectedNationality = trim((string) $request->query('nationality', 'all'));

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

        $applicationsQuery = StudentSchoolApplication::query()
            ->with(['student.creator', 'school'])
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

        if ($selectedAgent !== 'all' && ctype_digit($selectedAgent)) {
            $agentId = (int) $selectedAgent;

            $applicationsQuery->whereHas('student', function ($q) use ($agentId) {
                $q->where('created_by', $agentId);
            });
        }

        if ($selectedNationality !== 'all' && $selectedNationality !== '') {
            $applicationsQuery->whereHas('student', function ($q) use ($selectedNationality) {
                $q->where('nationality', $selectedNationality);
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

        $rows = $applications->map(function ($application) use ($requiredDocs, $submittedDocs) {
            $student = $application->student;
            $studentSubmittedDocIds = $submittedDocs->get($student->id, []);

            $docOutput = $requiredDocs->map(function ($req) use ($studentSubmittedDocIds) {
                $dt = $req->documentType;

                return [
                    'name' => $dt->doc_name,
                    'submitted' => in_array($dt->id, $studentSubmittedDocIds),
                ];
            });

            return [
                'application' => $application,
                'student' => $student,
                'docs' => $docOutput,
            ];
        });

        return view('school.dashboard', [
            'school' => $school,
            'user' => $user,
            'rows' => $rows,
            'intakes' => $intakes,
            'agents' => $agents,
            'nationalities' => $nationalities,
            'selectedNationality' => $selectedNationality,
            'selectedIntake' => $selectedIntake,
            'selectedAgent' => $selectedAgent,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function updateStatus(Request $request, StudentSchoolApplication $application): RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->role === 'school', 403);

        $user = Auth::user();
        abort_unless($user->school_id && (int) $application->school_id === (int) $user->school_id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', $this->allowedStatuses)],
        ]);

        $application->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('school.dashboard')
            ->with('success', 'Application status updated successfully.');
    }
}