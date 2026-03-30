<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentZipController extends Controller
{
    public function download(Student $student)
    {
        $user = Auth::user();

        $this->authorizeDownload($user, $student);

        $documents = StudentDocument::with(['documentType'])
            ->where('student_id', $student->id)
            ->orderBy('doc_type_id')
            ->get();

        if ($documents->isEmpty()) {
            return back()->with('error', 'No uploaded files found for this student.');
        }

        $zipFileName = 'student_' . $student->id . '_' . $this->safeName($student->student_name) . '_documents.zip';
        $tempZipPath = storage_path('app/temp/' . $zipFileName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        if (file_exists($tempZipPath)) {
            @unlink($tempZipPath);
        }

        $zip = new ZipArchive();

        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create ZIP file.');
        }

        $addedCount = 0;

        foreach ($documents as $document) {
            if (empty($document->file_path)) {
                continue;
            }

            if (!Storage::disk('public')->exists($document->file_path)) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($document->file_path);

            if (!file_exists($absolutePath)) {
                continue;
            }

            $schoolName = $this->safeName($document->school?->name ?: 'No_School');
            $docName = $this->safeName($document->documentType?->doc_name ?: 'Document');
            $originalName = basename($document->file_name ?: $document->file_path);

            $zipEntryName = $schoolName . '/' . $docName . '__' . $originalName;

            // avoid duplicate names inside zip
            $zipEntryName = $this->uniqueZipEntryName($zip, $zipEntryName);

            if ($zip->addFile($absolutePath, $zipEntryName)) {
                $addedCount++;
            }
        }

        $zip->close();

        if ($addedCount === 0) {
            if (file_exists($tempZipPath)) {
                @unlink($tempZipPath);
            }

            return back()->with('error', 'No physical files were found to add into ZIP.');
        }

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    private function authorizeDownload($user, Student $student): void
    {
        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'agent' && (int) $student->created_by === (int) $user->id) {
            return;
        }

        if ($user->role === 'student' && (int) $student->user_id === (int) $user->id) {
            return;
        }

        if ($user->role === 'school') {
            $hasApplicationForSchool = $student->applications()
                ->where('school_id', $user->school_id)
                ->exists();

            if ($hasApplicationForSchool) {
                return;
            }
        }

        abort(403);
    }

    private function safeName(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $value);
        $value = preg_replace('/_+/', '_', $value);
        $value = trim($value, '_');

        return $value !== '' ? $value : 'file';
    }

    private function uniqueZipEntryName(ZipArchive $zip, string $entryName): string
    {
        $finalName = $entryName;
        $i = 1;

        while ($zip->locateName($finalName) !== false) {
            $pathInfo = pathinfo($entryName);
            $dir = ($pathInfo['dirname'] ?? '.') !== '.' ? $pathInfo['dirname'] . '/' : '';
            $filename = $pathInfo['filename'] ?? 'file';
            $ext = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

            $finalName = $dir . $filename . '_' . $i . $ext;
            $i++;
        }

        return $finalName;
    }
}