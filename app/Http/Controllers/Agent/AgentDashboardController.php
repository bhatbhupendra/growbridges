<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOwnProfileRequest;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AgentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->check() && auth()->user()->role === 'agent', 403);

        $agent = auth()->user();

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

            // GLOBAL student documents, not school-specific
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

        return view('agent.dashboard', [
            'agent' => $agent,
            'students' => $studentRows,
            'intakes' => $intakes,
            'schools' => $schools,
            'selectedIntake' => $selectedIntake,
            'selectedSchool' => $selectedSchool,
        ]);
    }

    public function updateProfile(UpdateOwnProfileRequest $request): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'agent', 403);

        $agent = auth()->user();

        $data = [
            'name' => trim($request->name),
            'email' => trim($request->email),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $agent->update($data);

        return redirect()
            ->route('agent.dashboard')
            ->with('success', 'Profile updated successfully!');
    }
}