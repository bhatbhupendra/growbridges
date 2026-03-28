<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function show(Request $request, User $user): View
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
        $student = Student::with([
            'applications.school',
            'applications' => function ($q) {
                $q->latest();
            },
        ])->where('user_id', $user->id)->firstOrFail();
        return view('admin.student.show', compact('student', 'user'));
    }
}
