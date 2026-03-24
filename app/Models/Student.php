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
        'japanese_training_hours',
        'sponsor_name',
        'sponsor_relationship',
        'intake',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'graduation_year' => 'integer',
            'academic_gap_years' => 'integer',
        ];
    }

    // -------------------------
    // RELATIONSHIPS
    // -------------------------

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
        )->withPivot([
            'status',
            'assigned_by',
            'applied_by',
            'applied_at',
        ])->withTimestamps();
    }

    public function studentProfile()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}