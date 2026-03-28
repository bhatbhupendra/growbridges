<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentSchoolApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentFormController extends Controller
{
    public function create()
    {
        $schools = School::orderBy('name')->get();

        return view('student.create', compact('schools'));
    }

    public function store(StudentRequest $request)
    {
        DB::transaction(function () use ($request, &$student) {
            $data = $request->validated();

            $data['created_by'] = auth()->id();

            if (!empty($data['dob'])) {
                $data['age'] = Carbon::parse($data['dob'])->age;
            }

            $student = Student::create($data);

            // old system used default school_id = 35
            // new system must use student_school_applications
            $defaultSchoolId = 1;

            if (School::whereKey($defaultSchoolId)->exists()) {
                StudentSchoolApplication::create([
                    'student_id'   => $student->id,
                    'school_id'    => $defaultSchoolId,
                    'status'       => 'pending',
                    'assigned_by'  => auth()->id(),
                    'applied_by'   => auth()->id(),
                    'applied_at'   => now(),
                ]);
            }
        });

        return redirect()
            ->route('agent.dashboard')
            ->with('success', 'Student added successfully.');
    }

    public function edit(Student $student)
    {
        $schools = School::orderBy('name')->get();

        return view('agent.dashboard', compact('student', 'schools'));
    }

    public function update(StudentRequest $request, Student $student)
    {
        $data = $request->validated();

        if (!empty($data['dob'])) {
            $data['age'] = Carbon::parse($data['dob'])->age;
        } else {
            $data['age'] = null;
        }

        $student->update($data);

        return redirect()
            ->route('agent.dashboard')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()
            ->route('agent.dashboard')
            ->with('success', 'Student moved to recycle bin.');
    }
}