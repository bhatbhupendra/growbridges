<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::check() && Auth::user()->role === 'student', 403);
        $user = Auth::user();

        $student = Student::with([
            'applications.school',
            'applications' => function ($q) {
                $q->latest();
            },
        ])->where('user_id', $user->id)->firstOrFail();

        return view('studentD.dashboard', compact('student', 'user'));
    }
}