<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentSchoolApplication;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'role' => ['required', 'in:admin,agent,student,school'],
            'school_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->role === 'school' && trim((string) $value) === '') {
                        $fail('The school name field is required when role is school.');
                    }
                },
            ],
            'student_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            $role = $request->role;

            $user = User::create([
                'name' => trim($request->name),
                'email' => trim($request->email),
                'password' => Hash::make($request->password),
                'role' => $role,
                'school_id' => null,
            ]);

            // admin and agent -> user only
            if (in_array($role, ['admin', 'agent'], true)) {
                return $user;
            }

            // student -> create student profile if missing
            if ($role === 'student') {
                $existingStudent = Student::where('user_id', $user->id)->first();

                if (!$existingStudent) {
                    $student=Student::create([
                        'user_id' => $user->id,
                        'created_by' => $user->id,
                        'student_name' => trim($request->student_name ?: $request->name),
                        'email' => trim($request->email),
                    ]);
                }

                // ✅ Ensure Pre-School exists (id = 1)
                $preSchool = School::find(1);

                if ($preSchool) {
                    // ✅ Create application if not exists
                    StudentSchoolApplication::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'school_id' => 1,
                        ],
                        [
                            'status' => 'pending',
                            'assigned_by' => $user->id,
                            'applied_by' => $user->id,
                            'applied_at' => now(),
                        ]
                    );
                }


                return $user;
            }

            // school -> create or reuse school, then assign users.school_id
            if ($role === 'school') {
                $schoolName = trim((string) $request->school_name);

                if ($schoolName === '') {
                    abort(422, 'School name is required for school role.');
                }

                $school = School::firstOrCreate(
                    ['name' => $schoolName],
                    ['name' => $schoolName]
                );

                $user->update([
                    'school_id' => $school->id,
                ]);

                return $user->fresh();
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}