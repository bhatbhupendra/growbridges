<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\User;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function show(Request $request, School $school): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $selectedIntake = trim((string) $request->query('intake', 'all'));
        $selectedAgent = trim((string) $request->query('agent_id', 'all'));
        $selectedStatus = trim((string) $request->query('status', 'all'));


        $studentCount = StudentSchoolApplication::where('school_id', $school->id)->count();

        $user = User::where('school_id', $school->id)->first();
        // dd($user);

        $intakes = StudentSchoolApplication::query()
            ->join('students', 'students.id', '=', 'student_school_applications.student_id')
            ->where('student_school_applications.school_id', $school->id)
            ->whereNull('students.deleted_at')
            ->whereNotNull('students.intake')
            ->where('students.intake', '<>', '')
            ->distinct()
            ->orderByDesc('students.intake')
            ->pluck('students.intake');

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

        $query = StudentSchoolApplication::query()
            ->with(['student.applications.school', 'student.creator'])
            ->where('school_id', $school->id)
            ->whereHas('student', function ($q) {
                $q->whereNull('deleted_at');
            });

        if ($selectedIntake !== 'all' && $selectedIntake !== '') {
            $query->whereHas('student', function ($q) use ($selectedIntake) {
                $q->where('intake', $selectedIntake);
            });
        }

        if ($selectedAgent !== 'all' && ctype_digit((string) $selectedAgent)) {
            $agentId = (int) $selectedAgent;

            $query->whereHas('student', function ($q) use ($agentId) {
                $q->where('created_by', $agentId);
            });
        }

        $applications = $query->latest()->get();

        $rows = $applications->map(function ($application) use ($allSchools) {
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
                        ->where('school_id', $currentSchool->id)
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

            $availableSchools = $allSchools
                ->reject(fn ($s) => in_array((int) $s->id, $assignedSchoolIds, true))
                ->values();

            $photoDocument = StudentDocument::query()
                ->where('student_id', $student->id)
                ->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))
                ->whereHas('documentType', function ($q) {
                    $q->whereIn('file_type', ['jpg', 'jpeg']);
                })
                ->latest()
                ->first();

            return [
                'application' => $application,
                'student' => $student,
                'school' => $currentSchool,
                'docs' => $docOutput,
                'photo_url' => $photoDocument ? Storage::url($photoDocument->file_path) : null,
                'available_schools' => $availableSchools,
            ];
        });

        return view('admin.school.show', [
            'school' => $school,
            'studentCount' => $studentCount,
            'user'=>$user,
            'intakes' => $intakes,
            'agents' => $agents,
            'applications' => $applications,
            'rows' => $rows,
            'selectedIntake' => $selectedIntake,
            'selectedAgent' => $selectedAgent,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function assignStudentToSchool(Request $request, School $school, StudentSchoolApplication $application): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

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
                ->route('schools.show', $school)
                ->with('error', 'This student is already assigned to that school.');
        }

        return redirect()
            ->route('admin.school.show', $school)
            ->with('success', 'Student assigned to another school successfully.');
    }

    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $school->update([
            'name' => trim($request->name),
        ]);

        return redirect()
            ->route('admin.school.show', $school)
            ->with('success', 'School name updated successfully!');
    }

    public function destroy(School $school): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $studentCount = StudentSchoolApplication::where('school_id', $school->id)->count();

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