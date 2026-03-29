<?php

namespace App\Http\Controllers;

use App\Models\DocumentLiveChat;
use App\Models\DocumentType;
use App\Models\Notification;
use App\Models\School;
use App\Models\SchoolRequiredDoc;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentSchoolApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentFileController extends Controller
{
    public function show(Student $student, School $school): View
    {
        $user = Auth::user();

        $application = StudentSchoolApplication::where('student_id', $student->id)
            ->where('school_id', $school->id)
            ->firstOrFail();

        $this->authorizeView($user, $student, $school);

        $requiredDocs = SchoolRequiredDoc::with('documentType')
            ->where('school_id', $school->id)
            ->get();

        // CHANGED: student-based docs only
        $studentDocs = StudentDocument::with('documentType')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('doc_type_id');

        $groupedDocs = [];
        $submittedDocIds = [];

        $categoryOrder = [
            'Identity',
            'Educational',
            'Language',
            'JAPANESE TRANSLATED DOCUMENTS',
            'Financial',
            'Study Plan',
            'Additional',
        ];

        foreach ($requiredDocs as $req) {
            $dt = $req->documentType;
            if (!$dt) {
                continue;
            }

            $submitted = $studentDocs->get($dt->id);

            $row = [
                'doc_type_id' => $dt->id,
                'doc_name' => $dt->doc_name,
                'category' => $dt->category ?: 'Other',
                'file_type' => strtolower($dt->file_type ?: 'pdf'),
                'submitted_id' => $submitted?->id,
                'file_name' => $submitted?->file_name,
                'file_path' => $submitted ? Storage::url($submitted->file_path) : null,
                'uploaded_at' => optional($submitted?->updated_at)?->format('Y-m-d H:i:s'),
                'verify_status' => $submitted?->verify_status ?? 'pending',
                'verify_message' => $submitted?->verify_message,
                'verified_at' => optional($submitted?->verified_at)?->format('Y-m-d H:i:s'),
            ];

            $groupedDocs[$row['category']][] = $row;

            if (!empty($submitted?->id)) {
                $submittedDocIds[] = (int) $submitted->id;
            }
        }

        uksort($groupedDocs, function ($a, $b) use ($categoryOrder) {
            $ia = array_search($a, $categoryOrder, true);
            $ib = array_search($b, $categoryOrder, true);

            $ia = $ia === false ? 999 : $ia;
            $ib = $ib === false ? 999 : $ib;

            return $ia <=> $ib ?: strcmp($a, $b);
        });

        $chatMap = [];
        if (!empty($submittedDocIds)) {
            $chats = DocumentLiveChat::whereIn('document_id', array_unique($submittedDocIds))->get();
            foreach ($chats as $chat) {
                $chatMap[$chat->document_id] = $chat->chat ?? '';
            }
        }

        $photoUrl = $student->photo ? Storage::url($student->photo) : null;

        return view('student.file', compact(
            'student',
            'school',
            'application',
            'groupedDocs',
            'chatMap',
            'photoUrl'
        ));
    }

    public function upload(Request $request, Student $student, School $school): RedirectResponse
    {
        $user = Auth::user();
        $this->authorizeUpload($user, $student, $school);

        $request->validate([
            'doc_type_id' => ['required', 'integer', 'exists:document_types,id'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $docType = DocumentType::findOrFail($request->doc_type_id);

        $applicationExists = StudentSchoolApplication::where('student_id', $student->id)
            ->where('school_id', $school->id)
            ->exists();

        abort_unless($applicationExists, 404);

        $existingApproved = StudentDocument::where('student_id', $student->id)
            ->where('doc_type_id', $docType->id)
            ->where('verify_status', 'approved')
            ->first();

        if ($existingApproved) {
            return back()->with('error', 'This document is VERIFIED. Upload is locked.');
        }

        $required = SchoolRequiredDoc::where('school_id', $school->id)
            ->where('doc_type_id', $docType->id)
            ->exists();

        if (!$required) {
            return back()->with('error', 'This document is not required for this school.');
        }

        $file = $request->file('file');
        $expectedType = strtolower(trim($docType->file_type ?: 'pdf'));

        $ext = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        $rules = $this->allowedByType($expectedType);

        if (!in_array($ext, $rules['ext'], true)) {
            return back()->with('error', 'Invalid extension. Required: ' . strtoupper($expectedType));
        }

        if (!in_array($mime, $rules['mime'], true)) {
            $safeMimeBypass = in_array($expectedType, ['doc', 'xls'], true) && $mime === 'application/octet-stream';
            $safeZipBypass = in_array($expectedType, ['docx', 'xlsx'], true) && $mime === 'application/zip';

            if (!$safeMimeBypass && !$safeZipBypass) {
                return back()->with('error', 'Invalid file type (MIME). Required: ' . strtoupper($expectedType));
            }
        }

        $docNameSafe = Str::slug($docType->doc_name, '_');
        $studentNameSafe = Str::slug($student->student_name, '_');
        $today = now()->format('Y-m-d');

        $finalFileName = "{$docNameSafe}_{$studentNameSafe}_{$today}.{$ext}";
        $dir = "uploads/student_{$student->id}";

        if (Storage::disk('public')->exists("{$dir}/{$finalFileName}")) {
            $finalFileName = "{$docNameSafe}_{$studentNameSafe}_{$today}_" . time() . ".{$ext}";
        }

        $storedPath = $file->storeAs($dir, $finalFileName, 'public');

        $existing = StudentDocument::where('student_id', $student->id)
            ->where('doc_type_id', $docType->id)
            ->first();

        if ($existing) {
            if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }

            $existing->update([
                'file_name' => $finalFileName,
                'file_path' => $storedPath,
                'verify_status' => 'pending',
                'verify_message' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);

            $document = $existing;
        } else {
            $document = StudentDocument::create([
                'student_id' => $student->id,
                'doc_type_id' => $docType->id,
                'file_name' => $finalFileName,
                'file_path' => $storedPath,
                'verify_status' => 'pending',
            ]);
        }

        //saving photo the student->photo
        if (in_array(strtolower($docType->file_type), ['jpg', 'jpeg'])) {
            $student->photo = $storedPath;
            $student->save();
        }

        $this->notifyFileUpload($student, $school, $docType, $document);

        return redirect()
            ->route('student.file.show', [$student, $school])
            ->with('success', 'File uploaded successfully.');
    }


    public function verify(Request $request, Student $student, School $school, StudentDocument $document): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        if ($document->student_id !== $student->id) {
            abort(404);
        }

        $data = $request->validate([
            'action' => ['required', 'in:approved,disapproved'],
            'verify_message' => ['nullable', 'string'],
        ]);

        $document->update([
            'verify_status' => $data['action'],
            'verify_message' => $data['verify_message'] ?? null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $this->notifyVerifyStatus($student, $school, $document, $data['action']);

        return redirect()
            ->route('student.file.show', [$student, $school])
            ->with('success', 'Document verification saved.');
    }

    public function sendChat(Request $request, Student $student, School $school, StudentDocument $document): RedirectResponse
    {
        $user = Auth::user();

        abort_unless(in_array($user->role, ['admin', 'agent'], true), 403);

        if ($document->student_id !== $student->id) {
            abort(404);
        }

        $data = $request->validate([
            'chat_message' => ['required', 'string'],
        ]);

        $sender = $user->role === 'admin' ? 'ADMIN' : 'AGENT';
        $line = '[' . now()->format('Y-m-d H:i') . '] ' . $sender . ': ' . str_replace(["\r\n", "\r"], "\n", trim($data['chat_message']));

        $chat = DocumentLiveChat::firstOrNew([
            'document_id' => $document->id,
        ]);

        $existing = trim((string) $chat->chat);
        $chat->user_id = $user->id;
        $chat->chat = $existing !== '' ? $existing . "\n\n" . $line : $line;
        $chat->save();

        $this->notifyDocumentChat($student, $school, $document);

        return redirect()
            ->route('student.file.show', [$student, $school, 'doc' => $document->id, 'chat' => 1])
            ->with('success', 'Chat message sent.');
    }

    private function allowedByType(string $type): array
    {
        return match ($type) {
            'jpg', 'jpeg' => [
                'ext' => ['jpg', 'jpeg'],
                'mime' => ['image/jpeg'],
            ],
            'doc' => [
                'ext' => ['doc'],
                'mime' => ['application/msword', 'application/octet-stream'],
            ],
            'docx' => [
                'ext' => ['docx'],
                'mime' => [
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/zip',
                ],
            ],
            'xls' => [
                'ext' => ['xls'],
                'mime' => ['application/vnd.ms-excel', 'application/octet-stream'],
            ],
            'xlsx' => [
                'ext' => ['xlsx'],
                'mime' => [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/zip',
                ],
            ],
            default => [
                'ext' => ['pdf'],
                'mime' => ['application/pdf'],
            ],
        };
    }

    private function authorizeView($user, Student $student, School $school): void
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

        if ($user->role === 'school' && (int) $user->school_id === (int) $school->id) {
            return;
        }

        abort(403);
    }

    private function authorizeUpload($user, Student $student, School $school): void
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

        abort(403);
    }

    private function notifyFileUpload(Student $student, School $school, DocumentType $docType, StudentDocument $document): void
    {
        $targets = \App\Models\User::whereIn('role', ['admin'])
            ->orWhere(function ($q) use ($student) {
                if ($student->created_by) {
                    $q->where('id', $student->created_by);
                }
            })
            ->get();

        foreach ($targets as $target) {
            Notification::create([
                'notification_type' => 'File Uploaded',
                'title' => 'File Uploaded',
                'message' => $student->student_name . ' uploaded ' . $docType->doc_name . ' for ' . $school->name,
                'user_id' => $target->id,
                'student_id' => $student->id,
                'document_id' => $document->id,
                'created_by' => Auth::id(),
                'redirect_url' => route('student.file.show', [$student, $school]),
            ]);
        }
    }

    private function notifyVerifyStatus(Student $student, School $school, StudentDocument $document, string $status): void
    {
        $targets = \App\Models\User::query()
            ->where(function ($q) use ($student) {
                if ($student->user_id) {
                    $q->orWhere('id', $student->user_id);
                }
                if ($student->created_by) {
                    $q->orWhere('id', $student->created_by);
                }
            })
            ->get();

        foreach ($targets as $target) {
            Notification::create([
                'notification_type' => $status === 'approved' ? 'Accepted' : 'Rejected',
                'title' => $status === 'approved' ? 'Document Approved' : 'Document Disapproved',
                'message' => $student->student_name . ' document was ' . $status . ' for ' . $school->name,
                'user_id' => $target->id,
                'student_id' => $student->id,
                'document_id' => $document->id,
                'created_by' => Auth::id(),
                'redirect_url' => route('student.file.show', [$student, $school]),
            ]);
        }
    }

    private function notifyDocumentChat(Student $student, School $school, StudentDocument $document): void
    {
        $targets = \App\Models\User::query()
            ->where('role', 'admin')
            ->orWhere(function ($q) use ($student) {
                if ($student->created_by) {
                    $q->orWhere('id', $student->created_by);
                }
            })
            ->get();

        foreach ($targets as $target) {
            if ((int) $target->id === (int) Auth::id()) {
                continue;
            }

            Notification::create([
                'notification_type' => 'Document Chat',
                'title' => 'New Message on Document',
                'message' => Auth::user()->name . ' sent a message on document for ' . $student->student_name,
                'user_id' => $target->id,
                'student_id' => $student->id,
                'document_id' => $document->id,
                'created_by' => Auth::id(),
                'redirect_url' => route('student.file.show', [$student, $school, 'doc' => $document->id, 'chat' => 1]),
            ]);
        }
    }
}