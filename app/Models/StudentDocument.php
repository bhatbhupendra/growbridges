<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_id',
        'doc_type_id',
        'file_name',
        'file_path',
        'verify_status',
        'verify_message',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function chats(): HasMany
    {
        return $this->hasMany(DocumentLiveChat::class, 'document_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'document_id');
    }
}