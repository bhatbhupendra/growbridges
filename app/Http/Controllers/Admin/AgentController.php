<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function show(Request $request, User $agent): View
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless(in_array($agent->role, ['agent', 'user'], true), 404);

        $selectedIntake = trim((string) $request->query('intake', ''));
        $selectedSchool = trim((string) $request->query('school_id', 'all'));

        $intakes = Student::query()
            ->where('created_by', $agent->id)
            ->whereNull('deleted_at')
            ->whereNotNull('intake')
            ->where('intake', '<>', '')
            ->distinct()
            ->orderByDesc('intake')
            ->pluck('intake');

        if ($selectedIntake === '') {
            $selectedIntake = 'all';
        }

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

        $studentsQuery = Student::query()
            ->with(['applications.school'])
            ->where('created_by', $agent->id)
            ->whereNull('deleted_at')
            ->latest('id');

        if ($selectedIntake !== 'all' && $selectedIntake !== '') {
            $studentsQuery->where('intake', $selectedIntake);
        }

        if ($selectedSchool !== 'all' && ctype_digit((string) $selectedSchool)) {
            $schoolId = (int) $selectedSchool;

            $studentsQuery->whereHas('applications', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        $students = $studentsQuery->get();

        $studentRows = $students->map(function ($student) use ($selectedSchool) {
            $applications = $student->applications
                ->filter(fn ($app) => $app->school)
                ->values();

            // Global student documents, not school-based
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

            return [
                'student' => $student,
                'schools' => $schoolsData,
                'active_school_id' => $defaultSchool['id'] ?? null,
            ];
        });

        return view('admin.agents.show', [
            'agent' => $agent,
            'students' => $studentRows,
            'intakes' => $intakes,
            'schools' => $schools,
            'selectedIntake' => $selectedIntake,
            'selectedSchool' => $selectedSchool,
        ]);
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