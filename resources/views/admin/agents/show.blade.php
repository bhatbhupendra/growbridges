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
    width: 130px;
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

.modal-mini .modal-dialog {
    position: fixed;
    right: 16px;
    bottom: 16px;
    margin: 0;
    width: 430px;
    max-width: calc(100vw - 32px);
}

.modal-mini .modal-content {
    border-radius: 14px;
    box-shadow: 0 14px 30px rgba(0, 0, 0, .2);
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

.filter-bar .form-select,
.filter-bar .form-control {
    padding: .35rem .55rem;
    font-size: 12.5px;
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
</style>

<div class="container page-container small-ui">
    <div class="row g-3">
        <div class="col-lg-9">

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0">Agent / Consultancy View</h5>
                        <div class="text-muted" style="font-size:12px;">Students + document completion overview</div>
                    </div>
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">← Dashboard</a>
                        <span class="badge badge-soft">Admin View</span>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="m-0" style="font-weight:800;">Agent Information</h6>
                        <div class="text-muted" style="font-size:12px;">User ID: {{ $agent->id }}</div>
                    </div>
                    <span class="badge badge-soft">Role: {{ $agent->role }}</span>
                </div>
                <hr class="my-2">
                <div><b>Name:</b> {{ $agent->name }}</div>
                <div><b>Email:</b> {{ $agent->email }}</div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">Students List</h6>
                    @include('components.student-export-selected-modal', [
                        'modalId' => 'schoolStudentExportSelectedModal'
                    ])
                </div>

                <form method="GET" class="filter-bar row g-2 align-items-end mb-2">
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-weight:800;">Intake</label>
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
                        <label class="form-label mb-1" style="font-weight:800;">Nationality</label>
                        <select name="nationality" class="form-select">
                            <option value="all" {{ $selectedNationality === 'all' ? 'selected' : '' }}>All nationality</option>
                            @foreach($nationalities as $nationality)
                                <option value="{{ $nationality }}" {{ $selectedNationality === $nationality ? 'selected' : '' }}>
                                    {{ $nationality }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-weight:800;">School</label>
                        <select name="school_id" class="form-select">
                            <option value="all" {{ $selectedSchool === 'all' ? 'selected' : '' }}>All schools</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}"
                                    {{ (string) $selectedSchool === (string) $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-weight:800;">Status</label>
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
                        <button class="btn btn-sm btn-primary" type="submit">Apply Filter</button>
                    </div>

                    <div class="col-12">
                        <div class="text-muted" style="font-size:12px;">
                            Showing:
                            <b>{{ $selectedIntake === 'all' ? 'All intakes' : $selectedIntake }}</b> /
                            <b>{{ $selectedNationality === 'all' ? 'All nationalities' : $selectedNationality }}</b> /
                            <b>{{ $selectedSchool === 'all' ? 'All schools' : 'Selected school' }}</b> /
                            <b>{{ $selectedStatus === 'all' ? 'All status' : $selectedStatus }}</b>
                            <a class="ms-2" href="{{ route('admin.agents.show', $agent) }}">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:45px;" class="text-center">
                                    <input type="checkbox" onchange="toggleAllStudentExportCheckboxes(this)">
                                </th>
                                <th style="width:55px;">#</th>
                                <th>Student</th>
                                <th style="width:200px;">Assigned Schools</th>
                                <th style="width:360px;">Documents</th>
                                <th style="width:180px;">Photo</th>
                                <th style="width:180px;">Status</th>
                                <th style="width:220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $row)
                                @php
                                    $student = $row['student'];
                                    $schools = collect($row['schools']);
                                    $activeSchoolId = $row['active_school_id'];
                                    $activeSchool = $schools->firstWhere('id', $activeSchoolId) ?? $schools->first();

                                    $rawPhotoPath = trim((string) ($student->photo ?? ''));
                                    $rawPhotoPath = str_replace('\\', '/', $rawPhotoPath);
                                    $rawPhotoPath = preg_replace('#^/?storage/#', '', $rawPhotoPath);
                                    $rawPhotoPath = ltrim($rawPhotoPath, '/');
                                    $photoUrl = $rawPhotoPath !== '' ? asset('storage/' . $rawPhotoPath) : null;

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
                                        ? route('agent.remove-student-school', [$agent, $student, $currentAssigned['application_id']])
                                        : '';

                                    $initialDisabled = !$currentAssigned || $currentAssigned['is_current'];
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="student-export-checkbox" value="{{ $student->id }}"
                                            onchange="updateSelectedStudentCount()">
                                    </td>

                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        <div class="student-name">
                                            {{ $student->student_name }}
                                            @if(!empty($student->student_name_jp))
                                                <span class="text-primary">({{ $student->student_name_jp }})</span>
                                            @endif
                                        </div>

                                        <div class="student-meta">
                                            @if(!empty($student->gender))
                                                <span class="badge badge-soft me-1">Gender: {{ $student->gender }}</span>
                                            @endif
                                            @if(!empty($student->nationality))
                                                <span class="badge badge-soft me-1">Nationality: {{ $student->nationality }}</span>
                                            @endif
                                            @if(!empty($student->intake))
                                                <span class="badge badge-soft me-1">Intake: {{ $student->intake }}</span>
                                            @endif
                                            @if(!empty($student->age))
                                                Age: {{ $student->age }}
                                            @endif
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
                                                        data-remove-url="{{ route('agent.remove-student-school', [$agent, $student, $assignedSchool['application_id']]) }}"
                                                        data-status-url="{{ route('agent.applications.status', [$agent, $assignedSchool['application_id']]) }}"
                                                        data-view-url="{{ $schools->firstWhere('application_id', $assignedSchool['application_id'])['view_url'] ?? '#' }}"
                                                        data-docs='@json($schools->firstWhere("application_id", $assignedSchool["application_id"])["docs"] ?? [])'
                                                        {{ $currentAssigned && $currentAssigned['application_id'] == $assignedSchool['application_id'] ? 'selected' : '' }}
                                                    >
                                                        {{ $assignedSchool['school_name'] }}{{ $assignedSchool['is_current'] ? ' (Current)' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="doc-list" id="docs-{{ $index }}">
                                            @if($activeSchool && !empty($activeSchool['docs']))
                                                @foreach($activeSchool['docs'] as $doc)
                                                    <div class="{{ $doc['submitted'] ? 'doc-ok' : 'doc-miss' }}">
                                                        {{ $doc['submitted'] ? '✔' : '✖' }} {{ $doc['name'] }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No required documents set.</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        @if($photoUrl)
                                            <img src="{{ $photoUrl }}" class="thumb" alt="Student Photo">
                                        @else
                                            <span class="text-muted">No Photo</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="status-chip {{ $selectedStatusClass }}" id="status-chip-{{ $index }}">
                                            {{ ucfirst($selectedStatusValue) }}
                                        </span>

                                        @php
                                            $initialStatus = strtolower($currentAssigned['status'] ?? '');
                                        @endphp

                                        <form method="POST"
                                            action="{{ $currentAssigned ? route('agent.applications.status', [$agent, $currentAssigned['application_id']]) : '#' }}"
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
                                        <a href="{{ $activeSchool['view_url'] ?? '#' }}"
                                            id="view-btn-{{ $index }}"
                                            class="btn btn-sm btn-success w-100 mb-1 {{ $activeSchool ? '' : 'disabled' }}">
                                            View Student
                                        </a>

                                        <a href="{{ route('student.zip', $student) }}" class="btn btn-sm btn-primary w-100 mb-1">
                                            ZIP FILES
                                        </a>

                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary w-100 mb-1 open-assign-school-modal"
                                            data-student-name="{{ $student->student_name }}"
                                            data-assign-url="{{ route('agent.assign-student-school', [$agent, $student]) }}"
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
                                            <input type="hidden" name="current_application_id" id="current-application-id-{{ $index }}" value="{{ $currentAssigned['application_id'] ?? '' }}">

                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger w-100"
                                                id="remove-school-btn-{{ $index }}"
                                                {{ $initialDisabled ? 'disabled' : '' }}>
                                                Remove Selected School
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('student.destroy', $student) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                                onclick="return confirm('Move this student to recycle bin?')">
                                                Delete Student
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No students found for this filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-lg-3">
            <div class="card-box side-box">
                <h6 class="mb-2" style="font-weight:800;">About this page</h6>
                <div class="text-muted" style="font-size:12px; line-height:1.5;">
                    Filter students by intake, nationality, school, and status.
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Quick actions</div>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#editUserModal">
                        Edit User/Agent Info
                    </button>

                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                        Delete User/Agent
                    </button>
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Tips</div>
                <div class="p-2 rounded" style="background:#f8fafc; border:1px solid #e5e7eb;">
                    Use Reset to quickly show all students again.
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
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark" id="assignSchoolSubmitBtn">Assign School</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-mini" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:900;">Edit Agent</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('admin.agents.update', $agent) }}">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label" style="font-weight:900;">Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" value="{{ $agent->name }}"
                            required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" style="font-weight:900;">Email *</label>
                        <input type="email" name="email" class="form-control form-control-sm"
                            value="{{ $agent->email }}" required>
                    </div>

                    <div class="mb-1">
                        <label class="form-label" style="font-weight:900;">New Password (optional)</label>
                        <input type="password" name="password" class="form-control form-control-sm"
                            placeholder="Leave blank to keep old password">
                    </div>

                    <div class="text-muted mt-2" style="font-size:12px;">
                        If you set a new password, the agent must use it for next login.
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-mini" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:900;">Delete Agent</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <div class="p-2 rounded" style="background:#fff7ed; border:1px solid #fed7aa;">
                        <div style="font-weight:900;">This action cannot be undone.</div>
                        <div class="text-muted" style="font-size:12px;">
                            Agent: <b>{{ $agent->name }}</b><br>
                            Email: {{ $agent->email }}
                        </div>
                    </div>

                    <div class="text-muted mt-2" style="font-size:12px;">
                        Tip: If the agent has students, delete or move students first.
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
    const t = document.getElementById("toastMsg");
    if (t) {
        t.style.display = "block";
        setTimeout(() => {
            t.style.display = "none";
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
            const viewUrl = selectedOption.getAttribute('data-view-url') || '#';
            let docs = [];

            try {
                docs = JSON.parse(selectedOption.getAttribute('data-docs') || '[]');
            } catch (e) {
                docs = [];
            }

            const statusChip = document.getElementById('status-chip-' + row);
            const removeForm = document.getElementById('remove-school-form-' + row);
            const removeBtn = document.getElementById('remove-school-btn-' + row);
            const statusForm = document.getElementById('status-form-' + row);
            const statusSelect = document.getElementById('status-select-' + row);
            const docsEl = document.getElementById('docs-' + row);
            const viewBtn = document.getElementById('view-btn-' + row);
            const currentApplicationIdInput = document.getElementById('current-application-id-' + row);

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

            if (docsEl) {
                if (docs.length) {
                    docsEl.innerHTML = docs.map(doc => {
                        const cls = doc.submitted ? 'doc-ok' : 'doc-miss';
                        const icon = doc.submitted ? '✔' : '✖';
                        return `<div class="${cls}">${icon} ${doc.name}</div>`;
                    }).join('');
                } else {
                    docsEl.innerHTML = `<span class="text-muted">No required documents set.</span>`;
                }
            }

            if (viewBtn) {
                viewBtn.href = viewUrl || '#';
                if (viewUrl && viewUrl !== '#') {
                    viewBtn.classList.remove('disabled');
                } else {
                    viewBtn.classList.add('disabled');
                }
            }

            if (currentApplicationIdInput) {
                currentApplicationIdInput.value = selectedOption.value || '';
            }
        }

        dropdown.addEventListener('change', updateRowUI);
        updateRowUI();
    });
});
</script>
@endsection