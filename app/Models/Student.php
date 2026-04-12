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

        'pre_school_status',
        'admin_review_notes',
        'admin_reviewed_at',
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

        'admin_reviewed_at' => 'datetime',
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
    public function getPipelineStageAttribute(): string
    {
        $applications = $this->applications
            ->where('school_id', '!=', 1); // exclude Pre-School

        if ($applications->isEmpty()) {
            return $this->pre_school_status ?: 'new';
        }

        $statuses = $applications->pluck('status')->filter()->values();

        if ($statuses->contains(fn ($status) => in_array($status, ['selected', 'coe-applied', 'coe-granted']))) {
            return 'selected';
        }

        if ($statuses->contains('interview')) {
            return 'interview';
        }

        if ($applications->count() > 0 && $statuses->count() > 0 && $applications->count() === $statuses->filter(fn ($s) => $s === 'rejected')->count()) {
            return 'rejected_all';
        }

        return 'assigned';
    }
    public function getProfileChecklistAttribute(): array
    {
        return [
            'student_name'       => !empty($this->student_name),
            'student_name_jp'    => !empty($this->student_name_jp),
            'dob'                => !empty($this->dob),
            'gender'             => !empty($this->gender),
            'nationality'        => !empty($this->nationality),
            'phone'              => !empty($this->phone),
            'passport_number'    => !empty($this->passport_number),
            'current_address'    => !empty($this->current_address),
            'permanent_address'  => !empty($this->permanent_address),
            'photo'              => !empty($this->photo),
        ];
    }

    public function getProfileCompletionPercentAttribute(): int
    {
        $checklist = $this->profile_checklist;
        $total = count($checklist);
        $completed = collect($checklist)->filter()->count();

        return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
    }

    public function getMissingProfileFieldsAttribute(): array
    {
        return collect($this->profile_checklist)
            ->filter(fn ($completed) => !$completed)
            ->keys()
            ->map(fn ($field) => str_replace('_', ' ', ucfirst($field)))
            ->values()
            ->toArray();
    }
    public function strength()
    {
        return $this->hasOne(StudentStrength::class);
    }
}