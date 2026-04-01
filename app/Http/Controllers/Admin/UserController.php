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
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->get();

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
            'password' => ['nullable', 'min:6'],
        ]);

        DB::transaction(function () use ($request, $user) {
            $data = [
                'name' => trim($request->name),
                'email' => trim($request->email),
                'role' => $user->role, // keep old role always
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // keep related profile synced only, without changing role
            if ($user->role === 'student') {
                $student = Student::where('user_id', $user->id)->first();

                if ($student) {
                    $student->update([
                        'student_name' => $user->name,
                        'email' => $user->email,
                    ]);
                }
            }

            if ($user->role === 'school' && $user->school_id) {
                $school = School::find($user->school_id);

                if ($school) {
                    $school->update([
                        'name' => $user->name,
                    ]);
                }
            }
        });

        return redirect()->route('manage-users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            // If user is a student, delete student profile + related applications
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                StudentSchoolApplication::where('student_id', $student->id)->delete();
                $student->delete();
            }

            // If user is a school, delete school profile
            if ($user->school_id) {
                $school = School::find($user->school_id);

                // detach school from user first
                $user->update(['school_id' => null]);

                if ($school) {
                    $school->delete();
                }
            }

            // finally delete user
            $user->delete();
        });

        return back()->with('success', 'User deleted successfully');
    }
}