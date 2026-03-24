<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'school_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -------------------------
    // RELATIONSHIPS
    // -------------------------

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // If this user is a student account linked to one student profile
    public function studentProfile(): HasMany
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    // If this user is an agent/admin who created students
    public function createdStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'created_by');
    }

    public function assignedApplications(): HasMany
    {
        return $this->hasMany(StudentSchoolApplication::class, 'assigned_by');
    }

    public function appliedApplications(): HasMany
    {
        return $this->hasMany(StudentSchoolApplication::class, 'applied_by');
    }

    public function verifiedDocuments(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'verified_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function createdNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'created_by');
    }

    // -------------------------
    // ROLE HELPERS
    // -------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isSchool(): bool
    {
        return $this->role === 'school';
    }
}