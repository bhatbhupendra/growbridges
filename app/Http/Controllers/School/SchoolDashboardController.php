<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolRequiredDoc;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolDashboardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::check() && Auth::user()->role === 'school', 403);

        $user = Auth::user();
        abort_unless($user->school_id, 403);

        $school = $user->school;

        $selectedIntake = trim((string) $request->query('intake', 'all'));
        $selectedAgent = trim((string) $request->query('agent_id', 'all'));
        $selectedStatus = trim((string) $request->query('status', 'all'));

        $intakes = StudentSchoolApplication::query()
            ->join('students', 'students.id', '=', 'student_school_applications.student_id')
            ->where('student_school_applications.school_id', $school->id)
            ->whereNull('students.deleted_at')
            ->whereNotNull('students.intake')
            ->where('students.intake', '<>', '')
            ->distinct()
            ->orderByDesc('students.intake')
            ->pluck('students.intake');

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

        if ($selectedAgent !== 'all' && ctype_digit((string) $selectedAgent)) {
            $agentId = (int) $selectedAgent;

            $applicationsQuery->whereHas('student', function ($q) use ($agentId) {
                $q->where('created_by', $agentId);
            });
        }

        if ($selectedStatus !== 'all' && in_array($selectedStatus, ['pending', 'accepted', 'rejected', 'enrolled'], true)) {
            $applicationsQuery->where('status', $selectedStatus);
        }

        $applications = $applicationsQuery->get();

        $rows = $applications->map(function ($application) use ($school) {
            $student = $application->student;

            $requiredDocs = SchoolRequiredDoc::with('documentType')
                ->where('school_id', $school->id)
                ->get();

            $docOutput = collect();

            foreach ($requiredDocs as $req) {
                $dt = $req->documentType;
                if (!$dt) {
                    continue;
                }

                $submitted = StudentDocument::query()
                    ->where('student_id', $student->id)
                    ->where('school_id', $school->id)
                    ->where('doc_type_id', $dt->id)
                    ->exists();

                $docOutput->push([
                    'name' => $dt->doc_name,
                    'submitted' => $submitted,
                ]);
            }

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
            'status' => [
                'required',
                'in:interview,selected,rejected,coe-applied,coe-granted,coe-rejected,visa-granted,visa-rejected,withdrawal',
            ],
        ]);

        $application->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('school.dashboard')
            ->with('success', 'Application status updated successfully.');
    }
}