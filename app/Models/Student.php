<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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
        'passport_issue_date',
        'passport_expiry_date',
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

        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'marital_status',
    ];

    protected $casts = [
        'dob' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'graduation_year' => 'integer',
        'academic_gap_years' => 'integer',
        'japanese_training_hours' => 'integer',
        'age' => 'integer',
        'sponsor_annual_income_1' => 'decimal:2',
        'sponsor_savings_amount_1' => 'decimal:2',
        'sponsor_annual_income_2' => 'decimal:2',
        'sponsor_savings_amount_2' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(StudentSchoolApplication::class);
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(
            School::class,
            'student_school_applications',
            'student_id',
            'school_id'
        )->withPivot(['status', 'assigned_by', 'applied_by', 'applied_at'])
         ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }
}