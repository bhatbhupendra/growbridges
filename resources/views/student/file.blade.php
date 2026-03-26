@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}

.page-container {
    max-width: 1400px;
    margin: 22px auto;
}

.small-ui,
.small-ui * {
    font-size: 12.5px;
}

.card-box {
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
    background: #fff;
    margin-bottom: 16px;
}

.side-box {
    position: sticky;
    top: 16px;
}

.small-muted {
    font-size: 12px;
    color: #6c757d;
}

.badge-submitted {
    background: #198754;
}

.badge-missing {
    background: #dc3545;
}

.badge-soft {
    background: #eef2ff;
    color: #2b3a67;
    border: 1px solid #d6ddff;
    font-weight: 700;
}

.hr-tight {
    margin: 10px 0;
}

.table thead th {
    white-space: nowrap;
}

.file-cell {
    max-width: 340px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 8px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 12px;
    border: 1px solid transparent;
}

.chip-pending {
    background: #fff7ed;
    border-color: #fed7aa;
    color: #9a3412;
}

.chip-approved {
    background: #ecfdf5;
    border-color: #bbf7d0;
    color: #166534;
}

.chip-disapproved {
    background: #fef2f2;
    border-color: #fecaca;
    color: #991b1b;
}

.viewer-modal .modal-dialog {
    max-width: 980px;
}

.viewer-frame {
    width: 100%;
    height: 70vh;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fff;
    overflow: auto;
    position: relative;
}

.viewer-inner {
    transform-origin: top left;
}

.viewer-img {
    max-width: 100%;
    height: auto;
    display: block;
}

.toast-pop {
    position: fixed;
    right: 16px;
    bottom: 16px;
    z-index: 1080;
    min-width: 280px;
    max-width: 420px;
    border-radius: 12px;
    padding: 12px 14px;
    box-shadow: 0 14px 30px rgba(0, 0, 0, .18);
    display: none;
}

.chat-box {
    height: 320px;
    overflow: auto;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #f8fafc;
    padding: 10px;
    white-space: pre-wrap;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 12px;
}
</style>

<div class="container page-container small-ui">
    <div class="row g-3">
        <div class="col-lg-9">
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="m-0">Student Details</h5>
                        <div class="small-muted">School: <strong>{{ $school->name }}</strong></div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('student.edit', $student) }}" class="btn btn-sm btn-warning">Edit</a>
                        <a href="{{ route('student.zip', $student) }}" class="btn btn-sm btn-primary">
                            ZIP FILES
                        </a>
                    </div>
                </div>

                <hr class="hr-tight">

                <div class="row g-2">
                    <div class="col-md-3">
                        <div><b><u>Personal Information</u></b></div>
                        <div><b>Name:</b> {{ $student->student_name }} ({{ $student->student_name_jp }})</div>
                        <div><b>Gender:</b> {{ $student->gender }}</div>
                        <div><b>DOB:</b> {{ $student->dob?->format('Y-m-d') }} ({{ $student->age }})</div>
                        <div><b>Nationality:</b> {{ $student->nationality }}</div>
                        <div><b>Intake:</b> {{ $student->intake }}</div>
                        <div><b>School:</b> {{ $school->name }}</div>
                        <div><b>Marital Status:</b> {{ $student->marital_status }}</div>
                        <div><b>Email:</b> {{ $student->email }}</div>
                        <div><b>Phone:</b> {{ $student->phone }}</div>
                        <div><b>Permanent Address:</b> {{ $student->permanent_address }}</div>
                        <div><b>Current Address:</b> {{ $student->current_address }}</div>
                        <div><b><u>Family Information</u></b></div>
                        <div><b>Father Name:</b> {{ $student->father_name }}</div>
                        <div><b>Father Occupation:</b> {{ $student->father_occupation }}</div>
                        <div><b>Mother Name:</b> {{ $student->mother_name }}</div>
                        <div><b>Mother Occupation:</b> {{ $student->mother_occupation }}</div>
                    </div>

                    <div class="col-md-3">
                        <div><b><u>Academics Information</u></b></div>
                        <div><b>Highest Qualification:</b> {{ $student->highest_qualification }}</div>
                        <div><b>Last Institution:</b> {{ $student->last_institution_name }}</div>
                        <div><b>Graduate Year:</b> {{ $student->graduation_year }}</div>
                        <div><b>Academic Gap:</b> {{ $student->academic_gap_years }}</div>
                        <div><b><u>Japanese Language Information</u></b></div>
                        <div><b>Level:</b> {{ $student->japanese_level }}</div>
                        <div><b>Test Type:</b> {{ $student->japanese_test_type }}</div>
                        <div><b>Exam Score:</b> {{ $student->japanese_exam_score }}</div>
                        <div><b>Exam Date:</b> {{ $student->japanese_exam_date?->format('Y-m-d') }}</div>
                        <div><b>Training Hours:</b> {{ $student->japanese_training_hours }}</div>
                        <div><b><u>Passport Information</u></b></div>
                        <div><b>Number:</b> {{ $student->passport_number }}</div>
                        <div><b>Issue Date:</b> {{ $student->passport_issue_date?->format('Y-m-d') }}</div>
                        <div><b>Expiry Date:</b> {{ $student->passport_expiry_date?->format('Y-m-d') }}</div>
                    </div>

                    <div class="col-md-3">
                        <div><b><u>Sponsor 1 Information</u></b></div>
                        <div><b>Name:</b> {{ $student->sponsor_name_1 }}</div>
                        <div><b>Relationship:</b> {{ $student->sponsor_relationship_1 }}</div>
                        <div><b>Occupation:</b> {{ $student->sponsor_occupation_1 }}</div>
                        <div><b>Annual Income:</b> {{ $student->sponsor_annual_income_1 }}</div>
                        <div><b>Saving Amount:</b> {{ $student->sponsor_savings_amount_1 }}</div>

                        <div><b><u>Sponsor 2 Information</u></b></div>
                        <div><b>Name:</b> {{ $student->sponsor_name_2 }}</div>
                        <div><b>Relationship:</b> {{ $student->sponsor_relationship_2 }}</div>
                        <div><b>Occupation:</b> {{ $student->sponsor_occupation_2 }}</div>
                        <div><b>Annual Income:</b> {{ $student->sponsor_annual_income_2 }}</div>
                        <div><b>Saving Amount:</b> {{ $student->sponsor_savings_amount_2 }}</div>
                    </div>

                    <div class="col-md-3">
                        <div><b><u>Photo</u></b></div>
                        <div>
                            @if($photoUrl)
                            <img src="{{ $photoUrl }}" width="150" class="img-thumbnail" alt="Student Photo">
                            @else
                            <span class="text-muted">No Photo to Preview</span>
                            @endif
                        </div>
                        <div class="mt-2"><b>Information:</b> {{ $student->information }}</div>
                        <div><b>Career Path:</b> {{ $student->career_path }}</div>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div id="toastMsg" class="toast-pop" style="background:#198754;color:#fff;">
                <div style="font-weight:900;">Success</div>
                <div>{{ session('success') }}</div>
            </div>
            @endif

            @if(session('error'))
            <div id="toastMsg" class="toast-pop" style="background:#dc3545;color:#fff;">
                <div style="font-weight:900;">Error</div>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            @if(empty($groupedDocs))
            <div class="card-box">
                <div class="alert alert-warning mb-0 py-2">No required documents found for this school.</div>
            </div>
            @else
            @foreach($groupedDocs as $category => $docs)
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">{{ $category }} Documents</h6>
                    <span class="badge badge-soft">{{ count($docs) }} items</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:26%;">Document</th>
                                <th style="width:18%;">Status</th>
                                <th style="width:31%;">Uploaded File</th>
                                <th style="width:25%;">Upload / Verify</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($docs as $row)
                            @php
                            $docId = (int)($row['submitted_id'] ?? 0);
                            $ft = strtolower($row['file_type'] ?? 'pdf');
                            $verify = strtolower($row['verify_status'] ?? 'pending');
                            if (!in_array($verify, ['approved', 'disapproved'], true)) $verify = 'pending';

                            $chipClass = $verify === 'approved' ? 'chip-approved' : ($verify === 'disapproved' ?
                            'chip-disapproved' : 'chip-pending');
                            $chipText = strtoupper($verify);

                            $accept = match($ft) {
                            'jpg', 'jpeg' => 'image/jpeg',
                            'doc' => '.doc,application/msword',
                            'docx' => '.docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'xls' => '.xls,application/vnd.ms-excel',
                            'xlsx' => '.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            default => 'application/pdf,.pdf',
                            };

                            $chatText = $docId > 0 ? ($chatMap[$docId] ?? '') : '';
                            @endphp

                            @php
                            $rawPath = trim((string)($row['file_path'] ?? ''));
                            $rawPath = str_replace('\\', '/', $rawPath);
                            $rawPath = preg_replace('#^/?storage/#', '', $rawPath);
                            $rawPath = ltrim($rawPath, '/');

                            $fileUrl = asset('storage/' . $rawPath);
                            @endphp

                            <tr>
                                <td>{{ $row['doc_name'] }}</td>

                                <td>
                                    @if($row['submitted_id'])
                                    <span class="badge badge-submitted">SUBMITTED</span>
                                    <div class="small-muted">On: {{ $row['uploaded_at'] }}</div>

                                    <div class="mt-1">
                                        <span class="status-chip {{ $chipClass }}">{{ $chipText }}</span>
                                    </div>

                                    @if(!empty($row['verify_message']))
                                    <div class="small-muted mt-1"><b>Msg:</b> {{ $row['verify_message'] }}</div>
                                    @endif
                                    @else
                                    <span class="badge badge-missing">NOT SUBMITTED</span>
                                    @endif
                                </td>

                                <td>
                                    @if($row['submitted_id'])
                                    <div class="file-cell" title="{{ $row['file_name'] }}">
                                        {{ $row['file_name'] }}
                                    </div>

                                    <script type="text/plain" id="chatData_{{ $docId }}">{{ $chatText }}</script>

                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <button type="button" class="btn btn-sm btn-success btnViewFile"
                                            data-docid="{{ $docId }}" data-filetype="{{ $ft }}"
                                            data-fileurl="{{ $fileUrl }}" data-docname="{{ $row['doc_name'] }}"
                                            data-verifystatus="{{ $verify }}">
                                            View File
                                        </button>

                                        <a class="btn btn-sm btn-outline-dark" href="{{ $fileUrl }}" download>
                                            Download
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-primary btnLiveChat"
                                            data-docid="{{ $docId }}" data-docname="{{ $row['doc_name'] }}">
                                            Live Chat
                                        </button>
                                    </div>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($row['submitted_id'] && $verify === 'approved')
                                    <div class="p-2 rounded" style="background:#ecfdf5;border:1px solid #bbf7d0;">
                                        <div style="font-weight:900;color:#166534;">Verified ✅</div>
                                        <div class="small-muted">Upload locked (approved).</div>
                                    </div>
                                    @else
                                    @if(auth()->user()->role === 'admin' && $row['submitted_id'])
                                    <div class="d-grid gap-1 mb-2">
                                        <button type="button" class="btn btn-sm btn-dark btnOpenVerify"
                                            data-docid="{{ $docId }}" data-action="approved"
                                            data-docname="{{ $row['doc_name'] }}">
                                            Approve
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger btnOpenVerify"
                                            data-docid="{{ $docId }}" data-action="disapproved"
                                            data-docname="{{ $row['doc_name'] }}">
                                            Disapprove
                                        </button>
                                    </div>
                                    @endif

                                    <form method="POST" action="{{ route('student.file.upload', [$student, $school]) }}"
                                        enctype="multipart/form-data" class="d-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="doc_type_id" value="{{ (int)$row['doc_type_id'] }}">
                                        <input type="file" name="file" class="form-control form-control-sm"
                                            accept="{{ $accept }}" required>
                                        <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                    </form>

                                    <div class="small-muted mt-1">
                                        Required: <strong>{{ strtoupper($ft) }}</strong>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        <div class="col-lg-3">
            <div class="card-box side-box">
                <h6 class="mb-2" style="font-weight:800;">About this page</h6>
                <div class="text-muted" style="font-size:12px; line-height:1.5;">
                    Upload required documents and (Admin) verify them. Approved documents get locked.
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Tips</div>
                <div class="d-flex flex-column gap-2">
                    <div class="p-2 rounded" style="background:#f8fafc; border:1px solid #e5e7eb;">
                        Use <b>Live Chat</b> per file to communicate between admin and agent.
                    </div>
                    <div class="p-2 rounded" style="background:#f8fafc; border:1px solid #e5e7eb;">
                        Word/Excel preview not supported in browser — use <b>Download</b>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade viewer-modal" id="fileViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:14px;">
            <div class="modal-header py-2">
                <div>
                    <div style="font-weight:900;" id="viewerTitle">Document Viewer</div>
                    <div class="text-muted" style="font-size:12px;" id="viewerSub"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-dark" id="zoomOutBtn">-</button>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="zoomResetBtn">Reset</button>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="zoomInBtn">+</button>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-outline-dark" id="viewerDownloadBtn" download>Download</a>

                        @if(auth()->user()->role === 'admin')
                        <button type="button" class="btn btn-sm btn-dark" id="viewerApproveBtn">Approve</button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            id="viewerDisapproveBtn">Disapprove</button>
                        @endif
                    </div>
                </div>

                <div class="viewer-frame">
                    <div class="viewer-inner" id="viewerInner">
                        <iframe id="viewerPdf" src="" style="display:none;width:100%;height:70vh;border:0;"></iframe>
                        <img id="viewerImg" src="" alt="Image" class="viewer-img" style="display:none;">

                        <div id="viewerUnsupported" style="display:none;padding:14px;">
                            <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e5e7eb;">
                                <div style="font-weight:900;">Preview not available</div>
                                <div class="text-muted" style="font-size:12px;">
                                    This file type cannot be previewed in browser. Please download it.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verifyConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:900;" id="verifyTitle">Verify</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="verifyForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="action" id="verifyAction" value="">

                    <div class="p-2 rounded mb-2" style="background:#f8fafc;border:1px solid #e5e7eb;">
                        <div style="font-weight:900;" id="verifyDocName">Document</div>
                        <div class="text-muted" style="font-size:12px;">
                            Are you sure you want to <b id="verifyActionText">approve</b> this file?
                        </div>
                    </div>

                    <label class="form-label" style="font-weight:900;">Message / Comment</label>
                    <textarea name="verify_message" class="form-control form-control-sm" rows="3"
                        placeholder="Write reason / comment (optional)"></textarea>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark" id="verifySubmitBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="liveChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px;">
            <div class="modal-header py-2">
                <div>
                    <div style="font-weight:900;" id="chatTitle">Live Chat</div>
                    <div class="text-muted" style="font-size:12px;">Admin ↔ Agent chat for this file</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="chat-box" id="chatHistory">No chat yet...</div>

                <form method="POST" id="chatForm" class="mt-2">
                    @csrf
                    <label class="form-label fw-bold mb-1">New Message</label>
                    <textarea name="chat_message" class="form-control form-control-sm" rows="3"
                        placeholder="Type message..." required></textarea>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const t = document.getElementById("toastMsg");
    if (t) {
        t.style.display = "block";
        setTimeout(() => t.style.display = "none", 3500);
    }

    const fileViewerModal = new bootstrap.Modal(document.getElementById('fileViewerModal'));
    const verifyConfirmModal = new bootstrap.Modal(document.getElementById('verifyConfirmModal'));
    const liveChatModal = new bootstrap.Modal(document.getElementById('liveChatModal'));

    let currentZoom = 1;
    let currentDocId = 0;
    let currentDocName = "";
    let currentVerifyStatus = "pending";

    const viewerInner = document.getElementById('viewerInner');
    const viewerPdf = document.getElementById('viewerPdf');
    const viewerImg = document.getElementById('viewerImg');
    const viewerUnsupported = document.getElementById('viewerUnsupported');
    const viewerTitle = document.getElementById('viewerTitle');
    const viewerSub = document.getElementById('viewerSub');
    const viewerDownloadBtn = document.getElementById('viewerDownloadBtn');

    function applyZoom() {
        viewerInner.style.transform = "scale(" + currentZoom + ")";
    }

    function resetZoom() {
        currentZoom = 1;
        applyZoom();
    }

    document.getElementById('zoomInBtn')?.addEventListener('click', () => {
        currentZoom = Math.min(3, currentZoom + 0.15);
        applyZoom();
    });

    document.getElementById('zoomOutBtn')?.addEventListener('click', () => {
        currentZoom = Math.max(0.5, currentZoom - 0.15);
        applyZoom();
    });

    document.getElementById('zoomResetBtn')?.addEventListener('click', resetZoom);

    function setViewerMode(mode) {
        viewerPdf.style.display = "none";
        viewerImg.style.display = "none";
        viewerUnsupported.style.display = "none";
        viewerPdf.src = "";
        viewerImg.src = "";

        if (mode === "pdf") viewerPdf.style.display = "block";
        if (mode === "img") viewerImg.style.display = "block";
        if (mode === "unsupported") viewerUnsupported.style.display = "block";
    }

    document.querySelectorAll('.btnViewFile').forEach(btn => {
        btn.addEventListener('click', () => {
            const url = btn.dataset.fileurl || '';
            const ft = (btn.dataset.filetype || 'pdf').toLowerCase();
            currentDocId = parseInt(btn.dataset.docid || '0', 10);
            currentDocName = btn.dataset.docname || 'Document';
            currentVerifyStatus = btn.dataset.verifystatus || 'pending';

            viewerTitle.textContent = currentDocName;
            viewerSub.textContent = "Type: " + ft.toUpperCase() + " • Status: " +
                currentVerifyStatus.toUpperCase();
            viewerDownloadBtn.href = url;

            resetZoom();

            if (ft === 'jpg' || ft === 'jpeg') {
                setViewerMode("img");
                viewerImg.src = url;
            } else if (ft === 'pdf') {
                setViewerMode("pdf");
                viewerPdf.src = url;
            } else {
                setViewerMode("unsupported");
            }

            fileViewerModal.show();
        });
    });

    function openVerifyConfirm(action) {
        if (!currentDocId) return;

        const verifyForm = document.getElementById('verifyForm');
        verifyForm.action = "{{ url('/student/' . $student->id . '/file/' . $school->id . '/verify') }}/" +
            currentDocId;

        document.getElementById('verifyAction').value = action;
        document.getElementById('verifyTitle').textContent = (action === 'approved') ? "Approve Document" :
            "Disapprove Document";
        document.getElementById('verifyDocName').textContent = currentDocName;
        document.getElementById('verifyActionText').textContent = (action === 'approved') ? "approve" :
            "disapprove";

        const submitBtn = document.getElementById('verifySubmitBtn');
        submitBtn.textContent = (action === 'approved') ? "Confirm Approve" : "Confirm Disapprove";
        submitBtn.className = "btn btn-sm " + ((action === 'approved') ? "btn-dark" : "btn-danger");

        verifyConfirmModal.show();
    }

    document.getElementById('viewerApproveBtn')?.addEventListener('click', () => openVerifyConfirm('approved'));
    document.getElementById('viewerDisapproveBtn')?.addEventListener('click', () => openVerifyConfirm(
        'disapproved'));

    document.querySelectorAll('.btnOpenVerify').forEach(b => {
        b.addEventListener('click', () => {
            currentDocId = parseInt(b.dataset.docid || '0', 10);
            currentDocName = b.dataset.docname || 'Document';
            openVerifyConfirm((b.dataset.action || 'approved').toLowerCase());
        });
    });

    document.querySelectorAll('.btnLiveChat').forEach(b => {
        b.addEventListener('click', () => {
            const docId = parseInt(b.dataset.docid || '0', 10);
            const docName = b.dataset.docname || 'Document';

            document.getElementById('chatTitle').textContent = "Live Chat — " + docName;

            const chatForm = document.getElementById('chatForm');
            chatForm.action =
                "{{ url('/students/' . $student->id . '/file/' . $school->id . '/chat') }}/" +
                docId;

            const raw = document.getElementById('chatData_' + docId)?.textContent || '';
            const history = raw.trim() ? raw : "No chat yet...";
            const box = document.getElementById('chatHistory');
            box.textContent = history;

            setTimeout(() => {
                box.scrollTop = box.scrollHeight;
            }, 50);

            liveChatModal.show();
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const docAuto = parseInt(urlParams.get('doc') || '0', 10);
    if (urlParams.get('chat') === '1' && docAuto > 0) {
        const btn = document.querySelector('.btnLiveChat[data-docid="' + docAuto + '"]');
        if (btn) btn.click();
    }
});
</script>
@endsection