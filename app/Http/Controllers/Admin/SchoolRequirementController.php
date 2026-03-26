<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SchoolRequirementController extends Controller
{
    public function index(Request $request): View
    {
        $schoolId = (int) $request->query('school_id', 0);

        $schools = School::orderBy('name')->get();

        $categories = DocumentType::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->map(fn ($cat) => trim((string) $cat) === '' ? 'Other' : trim((string) $cat))
            ->filter()
            ->values();

        if ($categories->isEmpty()) {
            $categories = collect(['Other']);
        }

        $docTypesRaw = DocumentType::orderBy('category')
            ->orderBy('doc_name')
            ->get();

        $docTypes = $docTypesRaw->groupBy(function ($item) {
            $cat = trim((string) $item->category);
            return $cat === '' ? 'Other' : $cat;
        });

        $requiredMap = [];
        if ($schoolId > 0) {
            $requiredMap = SchoolRequiredDoc::where('school_id', $schoolId)
                ->pluck('doc_type_id')
                ->flip()
                ->toArray();
        }

        return view('admin.school_requirements.index', [
            'schools' => $schools,
            'categories' => $categories,
            'docTypes' => $docTypes,
            'schoolId' => $schoolId,
            'requiredMap' => $requiredMap,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'doc_type_ids' => ['nullable', 'array'],
            'doc_type_ids.*' => ['integer', 'exists:document_types,id'],
        ]);

        $schoolId = (int) $validated['school_id'];
        $selectedIds = collect($validated['doc_type_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        DB::transaction(function () use ($schoolId, $selectedIds) {
            SchoolRequiredDoc::where('school_id', $schoolId)->delete();

            foreach ($selectedIds as $docTypeId) {
                SchoolRequiredDoc::create([
                    'school_id' => $schoolId,
                    'doc_type_id' => $docTypeId,
                    'is_required' => true,
                ]);
            }
        });

        return redirect()
            ->route('admin.school-requirements.index', ['school_id' => $schoolId])
            ->with('success', 'Requirements saved successfully!');
    }
}