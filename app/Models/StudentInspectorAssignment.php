<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInspectorAssignment extends Model
{
    protected $fillable = [
        'student_id',
        'inspector_id',
        'assigned_by',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
