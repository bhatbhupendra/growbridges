<x-app-layout>
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

.table {
    table-layout: fixed;
}


.table thead th {
    white-space: nowrap;
}

.doc-list {
    font-size: 12px;
    line-height: 1.35;
    max-height: 160px;
    overflow: auto;
}

.doc-ok {
    color: #198754;
    font-weight: 800;
}

.doc-miss {
    color: #dc3545;
    font-weight: 800;
}

.student-name {
    font-weight: 800;
    font-size: 13px;
}

.student-meta {
    color: #6c757d;
    font-size: 12px;
}

.thumb {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #ddd;
    background: #fff;
}

.badge-soft {
    background: #eef2ff;
    color: #2b3a67;
    border: 1px solid #d6ddff;
    font-weight: 700;
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

.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
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

.chip-accepted {
    background: #ecfeff;
    border-color: #a5f3fc;
    color: #155e75;
}

.chip-rejected {
    background: #fef2f2;
    border-color: #fecaca;
    color: #991b1b;
}

.chip-enrolled {
    background: #ecfdf5;
    border-color: #bbf7d0;
    color: #166534;
}
</style>

<div class="container page-container small-ui">
    <div class="row g-3">
        <div class="col-lg-10">

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0">School Dashboard</h5>
                        <div class="text-muted" style="font-size:12px;">Assigned students for {{ $school->name }}</div>
                    </div>
                    <span class="badge badge-soft">School User</span>
                </div>
            </div>

            <div class="card-box">
                <div><b>School:</b> {{ $school->name }}</div>
                <div><b>Login User:</b> {{ $user->name }}</div>
                <div><b>Email:</b> {{ $user->email }}</div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">Assigned Students</h6>

                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge badge-soft">{{ $rows->count() }} Results</span>
                        <span class="badge badge-soft">
                            Selected: <span class="selected-student-count-label">0</span>
                        </span>

                        @include('components.student-export-selected-modal', [
                            'modalId' => 'schoolStudentExportSelectedModal'
                        ])
                    </div>
                </div>

                <form method="GET" class="row g-2 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Intake</label>
                        <select name="intake" class="form-select">
                            <option value="all" {{ $selectedIntake === 'all' ? 'selected' : '' }}>All intake</option>
                            @foreach($intakes as $intake)
                                <option value="{{ $intake }}" {{ $selectedIntake === $intake ? 'selected' : '' }}>
                                    {{ $intake }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Nationality</label>
                        <select name="nationality" class="form-select">
                            <option value="all" {{ $selectedNationality === 'all' ? 'selected' : '' }}>
                                All nationality
                            </option>

                            @foreach($nationalities as $nat)
                                <option value="{{ $nat }}" {{ $selectedNationality === $nat ? 'selected' : '' }}>
                                    {{ $nat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Agent</label>
                        <select name="agent_id" class="form-select">
                            <option value="all" {{ $selectedAgent === 'all' ? 'selected' : '' }}>All agents</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ (string) $selectedAgent === (string) $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All status</option>
                            <option value="interview" {{ $selectedStatus === 'interview' ? 'selected' : '' }}>School want to interview</option>
                            <option value="selected" {{ $selectedStatus === 'selected' ? 'selected' : '' }}>Selected</option>
                            <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="coe-applied" {{ $selectedStatus === 'coe-applied' ? 'selected' : '' }}>COE Applied</option>
                            <option value="coe-granted" {{ $selectedStatus === 'coe-granted' ? 'selected' : '' }}>COE Granted</option>
                            <option value="coe-rejected" {{ $selectedStatus === 'coe-rejected' ? 'selected' : '' }}>COE Rejected</option>
                            <option value="visa-granted" {{ $selectedStatus === 'visa-granted' ? 'selected' : '' }}>Visa Granted</option>
                            <option value="visa-rejected" {{ $selectedStatus === 'visa-rejected' ? 'selected' : '' }}>Visa Rejected</option>
                            <option value="withdrawal" {{ $selectedStatus === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-grid">
                        <button class="btn btn-sm btn-primary">Apply Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:35px;" class="text-center">
                                    <input type="checkbox" onchange="toggleAllStudentExportCheckboxes(this)">
                                </th>
                                <th style="width:25px;">#</th>
                                <th style="width:300px;">Student</th>
                                <th style="width:260px;">Documents</th>
                                <th style="width:150px;">Photo</th>
                                <th style="width:120px;">Status</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $index => $row)
                                @php
                                    $application = $row['application'];
                                    $student = $row['student'];
                                    $status = strtolower($application->status ?? '');

                                    $chipClass = match($status) {
                                        'interview' => 'chip-pending',
                                        'selected' => 'chip-accepted',
                                        'rejected' => 'chip-rejected',
                                        'coe-applied' => 'chip-pending',
                                        'coe-granted' => 'chip-accepted',
                                        'coe-rejected' => 'chip-rejected',
                                        'visa-granted' => 'chip-enrolled',
                                        'visa-rejected' => 'chip-rejected',
                                        'withdrawal' => 'chip-rejected',
                                        default => 'chip-pending',
                                    };

                                    $rawPath = trim((string) ($student->photo ?? ''));
                                    $rawPath = str_replace('\\', '/', $rawPath);
                                    $rawPath = preg_replace('#^/?storage/#', '', $rawPath);
                                    $rawPath = ltrim($rawPath, '/');
                                    $fileUrl = $rawPath !== '' ? asset('storage/' . $rawPath) : null;
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        <input
                                            type="checkbox"
                                            class="student-export-checkbox"
                                            value="{{ $student->id }}"
                                            onchange="updateSelectedStudentCount()"
                                        >
                                    </td>

                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        <div class="student-name">
                                            {{ $student->student_name }}
                                            @if(!empty($student->student_name_jp))
                                                <span class="text-primary">({{ $student->student_name_jp }})</span>
                                            @endif
                                        </div>

                                        <div class="student-meta mt-1">
                                            @if(!empty($student->gender))
                                                <span class="badge badge-soft me-1">Gender: {{ $student->gender }}</span>
                                            @endif

                                            @if(!empty($student->nationality))
                                                <span class="badge badge-soft me-1">Nationality: {{ $student->nationality }}</span>
                                            @endif

                                            @if(!empty($student->intake))
                                                <span class="badge badge-soft me-1">Intake: {{ $student->intake }}</span>
                                            @endif

                                            @if($student->creator)
                                                <span class="badge badge-soft me-1">Agent: {{ Str::limit($student->creator->name, 20) }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="doc-list">
                                            @if($row['docs']->isEmpty())
                                                <span class="text-muted">No required documents set.</span>
                                            @else
                                                @foreach($row['docs'] as $doc)
                                                    <div class="{{ $doc['submitted'] ? 'doc-ok' : 'doc-miss' }}">
                                                        {{ $doc['submitted'] ? '✔' : '✖' }} {{ $doc['name'] }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        @if($fileUrl)
                                            <img src="{{ $fileUrl }}" alt="Student Photo" class="thumb">
                                        @else
                                            <span class="text-muted">No photo</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="status-chip {{ $chipClass }}">
                                            {{ strtoupper($application->status ?? 'pending') }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('student.file.show', [$student, $school]) }}"
                                            class="btn btn-sm btn-primary w-100 mb-1">
                                            Open File
                                        </a>

                                        <form method="POST" action="{{ route('school.applications.status', $application) }}">
                                            @csrf

                                            <select name="status" class="form-select form-select-sm mb-1" required>
                                                <option value="" disabled {{ empty($application->status) ? 'selected' : '' }}>
                                                    Select a Status
                                                </option>
                                                <option value="interview" {{ $application->status === 'interview' ? 'selected' : '' }}>
                                                    School want to interview
                                                </option>
                                                <option value="selected" {{ $application->status === 'selected' ? 'selected' : '' }}>
                                                    Selected
                                                </option>
                                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>
                                                    Rejected
                                                </option>
                                                <option value="coe-applied" {{ $application->status === 'coe-applied' ? 'selected' : '' }}>
                                                    COE Applied
                                                </option>
                                                <option value="coe-granted" {{ $application->status === 'coe-granted' ? 'selected' : '' }}>
                                                    COE Granted
                                                </option>
                                                <option value="coe-rejected" {{ $application->status === 'coe-rejected' ? 'selected' : '' }}>
                                                    COE Rejected
                                                </option>
                                                <option value="visa-granted" {{ $application->status === 'visa-granted' ? 'selected' : '' }}>
                                                    Visa Granted
                                                </option>
                                                <option value="visa-rejected" {{ $application->status === 'visa-rejected' ? 'selected' : '' }}>
                                                    Visa Rejected
                                                </option>
                                                <option value="withdrawal" {{ $application->status === 'withdrawal' ? 'selected' : '' }}>
                                                    Withdrawal
                                                </option>
                                            </select>

                                            <button class="btn btn-sm btn-outline-dark w-100">Update Status</button>
                                        </form>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary w-100 mt-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#commentModal{{ $application->id }}">
                                            Comments
                                        </button>
                                    </td>
                                </tr>
                                <!-- comment on student -->
                                <div class="modal fade" id="commentModal{{ $application->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    Comments - {{ $student->student_name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="border rounded p-2 mb-3" style="max-height:300px; overflow:auto; background:#f8f9fa;">
                                                    @forelse($application->comments as $comment)
                                                        <div class="mb-2 p-2 border-bottom">
                                                            <div style="font-weight:700;">
                                                                {{ $comment->user->name ?? 'Unknown User' }}
                                                                <span class="text-muted" style="font-size:11px;">
                                                                    ({{ strtoupper($comment->user->role ?? '') }})
                                                                    • {{ $comment->created_at?->format('Y-m-d H:i') }}
                                                                </span>
                                                            </div>
                                                            <div style="white-space: pre-wrap;">{{ $comment->message }}</div>
                                                        </div>
                                                    @empty
                                                        <div class="text-muted">No comments yet.</div>
                                                    @endforelse
                                                </div>

                                                <form method="POST" action="{{ route('student.applications.comment', $application) }}">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold">Add Comment</label>
                                                        <textarea name="message" class="form-control" rows="4" required
                                                            placeholder="Write your comment..."></textarea>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        Send Comment
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No assigned students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-lg-2">
            <div class="card-box side-box">
                <h6 class="mb-2" style="font-weight:800;">About this page</h6>
                <div class="text-muted" style="font-size:12px; line-height:1.5;">
                    Review students assigned to your school, inspect document progress, and update application status.
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Quick actions</div>
                <div class="d-grid gap-2">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm">
                        Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div id="toastMsg" class="toast-pop" style="background:#198754; color:#fff;">
        <div style="font-weight:900;">Success</div>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error') || $errors->any())
    <div id="toastMsg" class="toast-pop" style="background:#dc3545; color:#fff;">
        <div style="font-weight:900;">Error</div>
        <div>{{ session('error') ?: 'Please check the form.' }}</div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const t = document.getElementById('toastMsg');
    if (t) {
        t.style.display = 'block';
        setTimeout(() => {
            t.style.display = 'none';
        }, 3500);
    }
});
</script>
</x-app-layout>