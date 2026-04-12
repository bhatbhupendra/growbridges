<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentStrength extends Model
{
    protected $fillable = [
        'student_id',
        'overall',
        'hiragana',
        'katagana',
        'numbers',
        'interview',
    ];

    protected $casts = [
        'overall' => 'integer',
        'hiragana' => 'integer',
        'katagana' => 'integer',
        'numbers' => 'integer',
        'interview' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}