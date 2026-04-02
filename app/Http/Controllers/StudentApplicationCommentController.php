<?php

namespace App\Http\Controllers;

use App\Models\StudentApplicationComment;
use App\Models\StudentSchoolApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentApplicationCommentController extends Controller
{
    public function store(Request $request, StudentSchoolApplication $application)
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();

        // allow only admin + school
        abort_unless(in_array($user->role, ['admin', 'school']), 403);

        // school can only comment on its own application
        if ($user->role === 'school') {
            abort_unless(
                $user->school_id && (int) $application->school_id === (int) $user->school_id,
                403
            );
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        StudentApplicationComment::create([
            'student_school_application_id' => $application->id,
            'user_id' => $user->id,
            'message' => $data['message'],
        ]);

        return back()->with('success', 'Comment added successfully.');
    }
}