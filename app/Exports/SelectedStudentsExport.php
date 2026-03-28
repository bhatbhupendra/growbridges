<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SelectedStudentsExport implements FromCollection, WithHeadings
{
    protected array $studentIds;
    protected array $fields;

    public function __construct(array $studentIds, array $fields)
    {
        $this->studentIds = $studentIds;
        $this->fields = $fields;
    }

    public function collection(): Collection
    {
        $students = Student::query()
            ->with(['creator', 'applications.school'])
            ->whereIn('id', $this->studentIds)
            ->get();

        return $students->map(function ($student) {
            $row = [];

            foreach ($this->fields as $field) {
                $row[$this->label($field)] = $this->value($student, $field);
            }

            return $row;
        });
    }

    public function headings(): array
    {
        return collect($this->fields)
            ->map(fn ($field) => $this->label($field))
            ->toArray();
    }

    protected function label(string $field): string
    {
        return match ($field) {
            'id' => 'ID',
            'user_id' => 'User ID',
            'created_by' => 'Created By ID',
            'student_name' => 'Student Name',
            'student_name_jp' => 'Student Name JP',
            'email' => 'Email',
            'gender' => 'Gender',
            'dob' => 'Date of Birth',
            'age' => 'Age',
            'nationality' => 'Nationality',
            'phone' => 'Phone',
            'passport_number' => 'Passport Number',
            'current_address' => 'Current Address',
            'permanent_address' => 'Permanent Address',
            'highest_qualification' => 'Highest Qualification',
            'last_institution_name' => 'Last Institution Name',
            'graduation_year' => 'Graduation Year',
            'academic_gap_years' => 'Academic Gap Years',
            'japanese_level' => 'Japanese Level',
            'japanese_test_type' => 'Japanese Test Type',
            'japanese_exam_score' => 'Japanese Exam Score',
            'japanese_training_hours' => 'Japanese Training Hours',
            'sponsor_name' => 'Sponsor Name',
            'sponsor_relationship' => 'Sponsor Relationship',

            'sponsor_name_1' => 'Sponsor 1 Name',
            'sponsor_relationship_1' => 'Sponsor 1 Relationship',
            'sponsor_occupation_1' => 'Sponsor 1 Occupation',
            'sponsor_annual_income_1' => 'Sponsor 1 Annual Income',
            'sponsor_savings_amount_1' => 'Sponsor 1 Savings Amount',

            'sponsor_name_2' => 'Sponsor 2 Name',
            'sponsor_relationship_2' => 'Sponsor 2 Relationship',
            'sponsor_occupation_2' => 'Sponsor 2 Occupation',
            'sponsor_annual_income_2' => 'Sponsor 2 Annual Income',
            'sponsor_savings_amount_2' => 'Sponsor 2 Savings Amount',

            'intake' => 'Intake',
            'photo' => 'Photo',
            'career_path' => 'Career Path',

            'creator_name' => 'Created By Name',
            'schools' => 'Assigned Schools',
            'application_statuses' => 'Application Statuses',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',

            default => ucwords(str_replace('_', ' ', $field)),
        };
    }

    protected function value($student, string $field): mixed
    {
        return match ($field) {
            'id' => $student->id,
            'user_id' => $student->user_id,
            'created_by' => $student->created_by,
            'student_name' => $student->student_name,
            'student_name_jp' => $student->student_name_jp,
            'email' => $student->email,
            'gender' => $student->gender,
            'dob' => $student->dob,
            'age' => $student->age,
            'nationality' => $student->nationality,
            'phone' => $student->phone,
            'passport_number' => $student->passport_number,
            'current_address' => $student->current_address,
            'permanent_address' => $student->permanent_address,
            'highest_qualification' => $student->highest_qualification,
            'last_institution_name' => $student->last_institution_name,
            'graduation_year' => $student->graduation_year,
            'academic_gap_years' => $student->academic_gap_years,
            'japanese_level' => $student->japanese_level,
            'japanese_test_type' => $student->japanese_test_type,
            'japanese_exam_score' => $student->japanese_exam_score,
            'japanese_training_hours' => $student->japanese_training_hours,
            'sponsor_name' => $student->sponsor_name,
            'sponsor_relationship' => $student->sponsor_relationship,

            'sponsor_name_1' => $student->sponsor_name_1,
            'sponsor_relationship_1' => $student->sponsor_relationship_1,
            'sponsor_occupation_1' => $student->sponsor_occupation_1,
            'sponsor_annual_income_1' => $student->sponsor_annual_income_1,
            'sponsor_savings_amount_1' => $student->sponsor_savings_amount_1,

            'sponsor_name_2' => $student->sponsor_name_2,
            'sponsor_relationship_2' => $student->sponsor_relationship_2,
            'sponsor_occupation_2' => $student->sponsor_occupation_2,
            'sponsor_annual_income_2' => $student->sponsor_annual_income_2,
            'sponsor_savings_amount_2' => $student->sponsor_savings_amount_2,

            'intake' => $student->intake,
            'photo' => $student->photo,
            'career_path' => $student->career_path,

            'creator_name' => $student->creator?->name,
            'schools' => $student->applications
                ->pluck('school.name')
                ->filter()
                ->unique()
                ->implode(', '),

            'application_statuses' => $student->applications
                ->map(fn ($app) => ($app->school?->name ?? 'Unknown School') . ': ' . ($app->status ?? 'pending'))
                ->implode(' | '),

            'created_at' => optional($student->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($student->updated_at)->format('Y-m-d H:i:s'),

            default => data_get($student, $field),
        };
    }
}