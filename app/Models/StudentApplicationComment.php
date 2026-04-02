<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentApplicationComment extends Model
{
    protected $fillable = [
        'student_school_application_id',
        'user_id',
        'message',
    ];

    public function application()
    {
        return $this->belongsTo(StudentSchoolApplication::class, 'student_school_application_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}