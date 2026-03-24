<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentLiveChat extends Model
{
    use HasFactory;

    protected $table = 'document_live_chat';

    protected $fillable = [
        'document_id',
        'user_id',
        'chat',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(StudentDocument::class, 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}