<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'intake' => ['required', 'string', 'max:255'],
            'student_name' => ['required', 'string', 'max:255'],
            'student_name_jp' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'dob' => ['required', 'date'],

            'nationality' => [
                'nullable',
                'string',
                'in:NEPAL,INDIA,BANGLADESH,SRILANKA,CAMEROON,TURKEY,AMERICA,UZBEKISTAN,OTHER'
            ],

            'phone' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:100'],
            'passport_issue_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date', 'after_or_equal:passport_issue_date'],
            'current_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],

            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'highest_qualification' => ['required', 'string', 'max:255'],
            'last_institution_name' => ['required', 'string', 'max:255'],
            'graduation_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'academic_gap_years' => ['nullable', 'integer', 'min:0', 'max:50'],

            'japanese_level' => ['nullable', 'string', 'max:100'],
            'japanese_test_type' => ['nullable', 'string', 'max:100'],
            'japanese_exam_score' => ['nullable', 'string', 'max:100'],
            'japanese_training_hours' => ['nullable', 'integer', 'min:0', 'max:50000'],

            'sponsor_name_1' => ['nullable', 'string', 'max:255'],
            'sponsor_relationship_1' => ['nullable', 'string', 'max:255'],
            'sponsor_occupation_1' => ['nullable', 'string', 'max:255'],
            'sponsor_annual_income_1' => ['nullable', 'numeric', 'min:0'],
            'sponsor_savings_amount_1' => ['nullable', 'numeric', 'min:0'],

            'sponsor_name_2' => ['nullable', 'string', 'max:255'],
            'sponsor_relationship_2' => ['nullable', 'string', 'max:255'],
            'sponsor_occupation_2' => ['nullable', 'string', 'max:255'],
            'sponsor_annual_income_2' => ['nullable', 'numeric', 'min:0'],
            'sponsor_savings_amount_2' => ['nullable', 'numeric', 'min:0'],

            'career_path' => ['nullable', 'string'],

            'father_name' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],

            'marital_status' => [
                'nullable',
                'string',
                'in:Single,Married,Divorced,Widowed'
            ],
        ];
    }
}