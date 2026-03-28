<?php

namespace App\Http\Controllers\Export;

use App\Exports\SelectedStudentsExport;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StudentExportController extends Controller
{
    public function exportSelected(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $allowedFields = [
            'id',
            'user_id',
            'created_by',
            'student_name',
            'student_name_jp',
            'email',
            'gender',
            'dob',
            'age',
            'nationality',
            'phone',
            'passport_number',
            'current_address',
            'permanent_address',
            'highest_qualification',
            'last_institution_name',
            'graduation_year',
            'academic_gap_years',
            'japanese_level',
            'japanese_test_type',
            'japanese_exam_score',
            'japanese_training_hours',
            'sponsor_name',
            'sponsor_relationship',

            'sponsor_name_1',
            'sponsor_relationship_1',
            'sponsor_occupation_1',
            'sponsor_annual_income_1',
            'sponsor_savings_amount_1',

            'sponsor_name_2',
            'sponsor_relationship_2',
            'sponsor_occupation_2',
            'sponsor_annual_income_2',
            'sponsor_savings_amount_2',

            'intake',
            'photo',
            'career_path',

            'creator_name',
            'schools',
            'application_statuses',
            'created_at',
            'updated_at',
        ];

        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*' => ['string', 'in:' . implode(',', $allowedFields)],
            'file_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = Auth::user();
        $studentIds = collect($data['student_ids'])->map(fn ($id) => (int) $id)->unique()->values();

        // role-based restriction
        $authorizedIds = Student::query()
            ->whereIn('id', $studentIds)
            ->when($user->role === 'agent', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->when($user->role === 'school', function ($q) use ($user) {
                $q->whereHas('applications', function ($sub) use ($user) {
                    $sub->where('school_id', $user->school_id);
                });
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        abort_if(empty($authorizedIds), 403, 'No valid students selected for export.');

        $fileName = trim((string) $request->input('file_name', 'students_export'));

        $safeFileName = str($fileName)->slug('_')->value();
        if ($safeFileName === '') {
            $safeFileName = 'students_export';
        }

        return Excel::download(
            new SelectedStudentsExport($authorizedIds, $data['fields']),
            $safeFileName . '.xlsx'
        );
    }
}