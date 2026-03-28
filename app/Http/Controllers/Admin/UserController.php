<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentSchoolApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.manageUsers.index', compact('users'));
    }

    public function create()
    {
        return view('admin.manageUsers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'role' => ['required', 'in:admin,agent,student,school'],
        ]);

        DB::transaction(function () use ($request) {
            $role = $request->role;

            $user = User::create([
                'name' => trim($request->name),
                'email' => trim($request->email),
                'password' => Hash::make($request->password),
                'role' => $role,
                'school_id' => null,
            ]);

            // admin & agent → only user
            if (in_array($role, ['admin', 'agent'], true)) {
                return;
            }

            // student → create student profile
            if ($role === 'student') {
                $student = Student::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'created_by' => auth()->id() ?? $user->id,
                        'student_name' => $user->name,
                        'email' => $user->email,
                    ]
                );

                // attach to Pre-School (id = 1)
                $preSchool = School::find(1);

                if ($preSchool) {
                    StudentSchoolApplication::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'school_id' => 1,
                        ],
                        [
                            'status' => 'pending',
                            'assigned_by' => auth()->id() ?? $user->id,
                            'applied_by' => auth()->id() ?? $user->id,
                            'applied_at' => now(),
                        ]
                    );
                }

                return;
            }

            // school → create or reuse school and assign
            if ($role === 'school') {
                $school = School::firstOrCreate(
                    ['name' => $user->name],
                    ['name' => $user->name]
                );

                $user->update([
                    'school_id' => $school->id,
                ]);

                return;
            }
        });

        return redirect()->route('manage-users.index')->with('success', 'User created');
    }

    public function edit(User $user)
    {
        return view('admin.manageUsers.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,agent,student,school'],
            'password' => ['nullable', 'min:6'],
        ]);

        DB::transaction(function () use ($request, $user) {
            $role = $request->role;

            $data = [
                'name' => trim($request->name),
                'email' => trim($request->email),
                'role' => $role,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($role !== 'school') {
                $data['school_id'] = null;
            }

            $user->update($data);

            // admin & agent → nothing extra
            if (in_array($role, ['admin', 'agent'], true)) {
                return;
            }

            // student → create/update student
            if ($role === 'student') {
                $student = Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_name' => $user->name,
                        'email' => $user->email,
                    ]
                );

                $preSchool = School::find(1);

                if ($preSchool) {
                    StudentSchoolApplication::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'school_id' => 1,
                        ],
                        [
                            'status' => 'pending',
                            'assigned_by' => auth()->id() ?? $user->id,
                            'applied_by' => auth()->id() ?? $user->id,
                            'applied_at' => now(),
                        ]
                    );
                }

                return;
            }

            // school → create/reuse school
            if ($role === 'school') {
                $school = School::firstOrCreate(
                    ['name' => $user->name],
                    ['name' => $user->name]
                );

                $user->update([
                    'school_id' => $school->id,
                ]);

                return;
            }
        });

        return redirect()->route('manage-users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted');
    }
}