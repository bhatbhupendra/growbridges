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
    width: 120px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #ddd;
    background: #fff;
}

.thumb-mini {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #eef2ff;
    color: #2b3a67;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    border: 1px solid #d6ddff;
    flex-shrink: 0;
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
    line-height: 1;
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

.assigned-school-dropdown {
    font-weight: 600;
    border-radius: 8px;
}

.status-wrap {
    min-width: 170px;
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
                    <div>
                        <span class="badge badge-soft">Admin View</span>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">← Dashboard</a>
                    </div>
                </div>
            </div>

            <div class="card-box">
                @if($school->id != 1)
                    <div><b>School:</b> {{ $school->name }}</div>
                    <div><b>Login User:</b> {{ $user->name ?? '-' }}</div>
                    <div><b>Email:</b> {{ $user->email ?? '-' }}</div>
                @else
                    <div><b>Welcome to</b> {{ $school->name }}</div>
                @endif
                <div><b>Total Students:</b> {{ $studentCount }}</div>
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
                    <div class="col-md-2">
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
                            <option value="all" {{ $selectedNationality === 'all' ? 'selected' : '' }}>All nationality</option>
                            @foreach($nationalities as $nationality)
                                <option value="{{ $nationality }}" {{ $selectedNationality === $nationality ? 'selected' : '' }}>
                                    {{ $nationality }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
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

                    <div class="col-md-2 d-grid">
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
                                <th>Student</th>
                                <th style="width:220px;">Assigned Schools</th>
                                <th style="width:300px;">Info</th>
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

                                    $rawPath = trim((string) ($student->photo ?? ''));
                                    $rawPath = str_replace('\\', '/', $rawPath);
                                    $rawPath = preg_replace('#^/?storage/#', '', $rawPath);
                                    $rawPath = ltrim($rawPath, '/');
                                    $fileUrl = $rawPath !== '' ? asset('storage/' . $rawPath) : null;

                                    $currentAssigned = collect($row['assigned_schools'])->firstWhere('is_current', true)
                                        ?? collect($row['assigned_schools'])->first();

                                    $selectedStatusValue = strtolower($currentAssigned['status'] ?? 'pending');

                                    $selectedStatusClass = match($selectedStatusValue) {
                                        'selected' => 'chip-accepted',
                                        'coe-granted' => 'chip-accepted',
                                        'rejected' => 'chip-rejected',
                                        'coe-rejected' => 'chip-rejected',
                                        'visa-rejected' => 'chip-rejected',
                                        'withdrawal' => 'chip-rejected',
                                        'visa-granted' => 'chip-enrolled',
                                        default => 'chip-pending',
                                    };

                                    $initialRemoveUrl = $currentAssigned
                                        ? route('admin.school.remove-student-school', [$school, $application, $currentAssigned['application_id']])
                                        : '';

                                    $initialDisabled = !$currentAssigned || $currentAssigned['is_current'];
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
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="w-100">
                                                <div class="student-name">
                                                    {{ $student->student_name }}
                                                    @if(!empty($student->student_name_jp))
                                                        <span class="text-primary">({{ $student->student_name_jp }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if(!empty($row['assigned_schools']) && count($row['assigned_schools']))
                                            <select class="form-select form-select-sm assigned-school-dropdown" data-row="{{ $index }}">
                                                @foreach($row['assigned_schools'] as $assignedSchool)
                                                    <option
                                                        value="{{ $assignedSchool['application_id'] }}"
                                                        data-status="{{ strtolower($assignedSchool['status'] ?? 'pending') }}"
                                                        data-is-current="{{ $assignedSchool['is_current'] ? '1' : '0' }}"
                                                        data-remove-url="{{ route('admin.school.remove-student-school', [$school, $application, $assignedSchool['application_id']]) }}"
                                                        data-status-url="{{ route('admin.school.applications.status', $assignedSchool['application_id']) }}"
                                                        {{ $currentAssigned && $currentAssigned['application_id'] == $assignedSchool['application_id'] ? 'selected' : '' }}
                                                    >
                                                        {{ $assignedSchool['school_name'] }}{{ $assignedSchool['is_current'] ? ' (Current)' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <span class="text-muted">No assigned schools</span>
                                        @endif
                                    </td>

                                    <td class="text-muted" style="font-size:12px;">
                                        {{ !empty($student->creator->name) ? 'Agent :-'.$student->creator->name. ' • ' : '' }}
                                        {{ !empty($student->gender) ? $student->gender . ' • ' : '' }}
                                        {{ !empty($student->nationality) ? $student->nationality . ' • ' : '' }}
                                        {{ !empty($student->age) ? 'Age: ' . $student->age . ' • ' : '' }}
                                        {{ !empty($student->intake) ? 'Intake: ' . $student->intake : '' }}
                                    </td>

                                    <td>
                                        @if($fileUrl)
                                            <img src="{{ $fileUrl }}" alt="Student Photo" class="thumb">
                                        @else
                                            <span class="text-muted">No photo</span>
                                        @endif
                                    </td>

                                    <td class="status-wrap">
                                        <span class="status-chip {{ $selectedStatusClass }}" id="status-chip-{{ $index }}">
                                            {{ ucfirst($selectedStatusValue) }}
                                        </span>

                                        @php
                                            $initialStatus = strtolower($currentAssigned['status'] ?? '');
                                        @endphp

                                        <form method="POST"
                                            action="{{ $currentAssigned ? route('admin.school.applications.status', $currentAssigned['application_id']) : '#' }}"
                                            id="status-form-{{ $index }}"
                                            class="mt-1">
                                            @csrf

                                            <select name="status" class="form-select form-select-sm mb-1" id="status-select-{{ $index }}" required>
                                                <option value="" disabled {{ empty($initialStatus) ? 'selected' : '' }}>
                                                    Select a Status
                                                </option>
                                                <option value="interview" {{ $initialStatus === 'interview' ? 'selected' : '' }}>
                                                    School want to interview
                                                </option>
                                                <option value="selected" {{ $initialStatus === 'selected' ? 'selected' : '' }}>
                                                    Selected
                                                </option>
                                                <option value="rejected" {{ $initialStatus === 'rejected' ? 'selected' : '' }}>
                                                    Rejected
                                                </option>
                                                <option value="coe-applied" {{ $initialStatus === 'coe-applied' ? 'selected' : '' }}>
                                                    COE Applied
                                                </option>
                                                <option value="coe-granted" {{ $initialStatus === 'coe-granted' ? 'selected' : '' }}>
                                                    COE Granted
                                                </option>
                                                <option value="coe-rejected" {{ $initialStatus === 'coe-rejected' ? 'selected' : '' }}>
                                                    COE Rejected
                                                </option>
                                                <option value="visa-granted" {{ $initialStatus === 'visa-granted' ? 'selected' : '' }}>
                                                    Visa Granted
                                                </option>
                                                <option value="visa-rejected" {{ $initialStatus === 'visa-rejected' ? 'selected' : '' }}>
                                                    Visa Rejected
                                                </option>
                                                <option value="withdrawal" {{ $initialStatus === 'withdrawal' ? 'selected' : '' }}>
                                                    Withdrawal
                                                </option>
                                            </select>

                                            <button class="btn btn-sm btn-outline-dark w-100">Update Status</button>
                                        </form>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('student.file.show', [$student, $school]) }}"
                                                class="btn btn-sm btn-success w-100 mb-1">
                                                View Student
                                            </a>

                                            <a class="btn btn-sm btn-primary w-100 mb-1"
                                                href="{{ route('student.zip', $student) }}">
                                                ZIP Files
                                            </a>

                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary w-100 mb-1 open-assign-school-modal"
                                                data-student-name="{{ $student->student_name }}"
                                                data-assign-url="{{ route('admin.school.assign-student-school', [$school, $application]) }}"
                                                data-available-schools='@json($row["available_schools"]->map(fn($s) => ["id" => $s->id, "name" => $s->name])->values())'>
                                                Assign School
                                            </button>

                                            <form method="POST"
                                                id="remove-school-form-{{ $index }}"
                                                action="{{ $initialRemoveUrl }}"
                                                onsubmit="return confirm('Remove this assigned school from the student?');"
                                                class="mb-1">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger w-100"
                                                        id="remove-school-btn-{{ $index }}"
                                                        {{ $initialDisabled ? 'disabled' : '' }}>
                                                    Remove Selected School
                                                </button>
                                            </form>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary w-100 mt-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#commentModal{{ $application->id }}">
                                                Comments
                                            </button>
                                        </div>
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
                                    <td colspan="9" class="text-center">No students enrolled yet.</td>
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
                    Review students assigned to this school, inspect document progress, and manage assigned student records.
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

<div class="modal fade modal-mini" id="assignSchoolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:800;">Assign Student to School</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="assignSchoolForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Student</div>
                        <div id="assignStudentName" style="font-weight:800;">Student Name</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Available Schools</label>
                        <select name="school_id" id="assignSchoolSelect" class="form-select" required>
                            <option value="">Select school</option>
                        </select>
                    </div>

                    <div id="assignNoSchoolsMsg" class="alert alert-warning py-2 mb-0 d-none">
                        No more schools available for this student.
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark" id="assignSchoolSubmitBtn">Assign School</button>
                </div>
            </form>
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

    const assignSchoolModalEl = document.getElementById('assignSchoolModal');
    const assignSchoolForm = document.getElementById('assignSchoolForm');
    const assignStudentName = document.getElementById('assignStudentName');
    const assignSchoolSelect = document.getElementById('assignSchoolSelect');
    const assignNoSchoolsMsg = document.getElementById('assignNoSchoolsMsg');
    const assignSchoolSubmitBtn = document.getElementById('assignSchoolSubmitBtn');

    if (assignSchoolModalEl) {
        const assignSchoolModal = new bootstrap.Modal(assignSchoolModalEl);

        document.querySelectorAll('.open-assign-school-modal').forEach(button => {
            button.addEventListener('click', function () {
                const studentName = this.getAttribute('data-student-name') || 'Student';
                const assignUrl = this.getAttribute('data-assign-url') || '';
                const availableSchools = JSON.parse(this.getAttribute('data-available-schools') || '[]');

                assignStudentName.textContent = studentName;
                assignSchoolForm.setAttribute('action', assignUrl);

                assignSchoolSelect.innerHTML = '<option value="">Select school</option>';

                if (availableSchools.length > 0) {
                    availableSchools.forEach(function (school) {
                        const option = document.createElement('option');
                        option.value = school.id;
                        option.textContent = school.name;
                        assignSchoolSelect.appendChild(option);
                    });

                    assignNoSchoolsMsg.classList.add('d-none');
                    assignSchoolSelect.disabled = false;
                    assignSchoolSubmitBtn.disabled = false;
                } else {
                    assignNoSchoolsMsg.classList.remove('d-none');
                    assignSchoolSelect.disabled = true;
                    assignSchoolSubmitBtn.disabled = true;
                }

                assignSchoolModal.show();
            });
        });
    }

    document.querySelectorAll('.assigned-school-dropdown').forEach(function (dropdown) {
        function updateRowUI() {
            const row = dropdown.dataset.row;
            const selectedOption = dropdown.options[dropdown.selectedIndex];

            const status = (selectedOption.getAttribute('data-status') || 'pending').toLowerCase();
            const isCurrent = selectedOption.getAttribute('data-is-current') === '1';
            const removeUrl = selectedOption.getAttribute('data-remove-url') || '';
            const statusUrl = selectedOption.getAttribute('data-status-url') || '';

            const statusChip = document.getElementById('status-chip-' + row);
            const removeForm = document.getElementById('remove-school-form-' + row);
            const removeBtn = document.getElementById('remove-school-btn-' + row);
            const statusForm = document.getElementById('status-form-' + row);
            const statusSelect = document.getElementById('status-select-' + row);

            if (statusChip) {
                statusChip.className = 'status-chip';

                if (status === 'selected' || status === 'coe-granted') {
                    statusChip.classList.add('chip-accepted');
                } else if (status === 'rejected' || status === 'coe-rejected' || status === 'visa-rejected' || status === 'withdrawal') {
                    statusChip.classList.add('chip-rejected');
                } else if (status === 'visa-granted') {
                    statusChip.classList.add('chip-enrolled');
                } else {
                    statusChip.classList.add('chip-pending');
                }

                statusChip.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            }

            if (removeForm && removeBtn) {
                removeForm.action = removeUrl;
                removeBtn.disabled = isCurrent;
            }

            if (statusForm) {
                statusForm.action = statusUrl;
            }

            if (statusSelect) {
                statusSelect.value = status;
            }
        }

        dropdown.addEventListener('change', updateRowUI);
        updateRowUI();
    });
});
</script>
@endsection