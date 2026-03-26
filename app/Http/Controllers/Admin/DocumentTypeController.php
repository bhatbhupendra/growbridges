<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentTypeRequest;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;

class DocumentTypeController extends Controller
{
    public function store(DocumentTypeRequest $request): RedirectResponse
    {
        $docName = trim($request->doc_name);
        $category = $request->category === '__custom__'
            ? $request->category_custom
            : $request->category;

        $category = DocumentType::cleanCategory($category);
        $fileType = DocumentType::normalizeFileType($request->file_type);
        $schoolId = (int) $request->input('current_school_id', 0);

        $exists = DocumentType::where('doc_name', $docName)
            ->where('category', $category)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('admin.school-requirements.index', $schoolId > 0 ? ['school_id' => $schoolId] : [])
                ->with('error', 'This document type already exists in this category.');
        }

        DocumentType::create([
            'doc_name' => $docName,
            'category' => $category,
            'file_type' => $fileType,
        ]);

        return redirect()
            ->route('admin.school-requirements.index', $schoolId > 0 ? ['school_id' => $schoolId] : [])
            ->with('success', 'Document type added successfully!');
    }

    public function update(DocumentTypeRequest $request, DocumentType $documentType): RedirectResponse
    {
        $docName = trim($request->doc_name);
        $category = $request->category === '__custom__'
            ? $request->category_custom
            : $request->category;

        $category = DocumentType::cleanCategory($category);
        $fileType = DocumentType::normalizeFileType($request->file_type);
        $schoolId = (int) $request->input('keep_school_id', 0);

        $exists = DocumentType::where('doc_name', $docName)
            ->where('category', $category)
            ->where('id', '!=', $documentType->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('admin.school-requirements.index', $schoolId > 0 ? ['school_id' => $schoolId] : [])
                ->with('error', 'Another document type already uses that name in that category.');
        }

        $documentType->update([
            'doc_name' => $docName,
            'category' => $category,
            'file_type' => $fileType,
        ]);

        return redirect()
            ->route('admin.school-requirements.index', $schoolId > 0 ? ['school_id' => $schoolId] : [])
            ->with('success', 'Document type updated successfully!');
    }
}