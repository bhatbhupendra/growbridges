<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_name',
        'category',
        'file_type',
    ];

    public function schoolRequiredDocs(): HasMany
    {
        return $this->hasMany(SchoolRequiredDoc::class, 'doc_type_id');
    }

    public function studentDocuments(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'doc_type_id');
    }
}