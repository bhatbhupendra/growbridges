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
    width: 120px;
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
        <div class="col-lg-9">

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
                @if($school->id != 1)
                <div><b>School:</b> {{ $school->name }}</div>
                <div><b>Login User:</b> {{ $user->name }}</div>
                <div><b>Email:</b> {{ $user->email }}</div>
                @else
                <div><b>Welcome to </b> {{ $school->name }}</div>
                @endif
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">Assigned Students</h6>
                    <div>
                        <span class="badge badge-soft">{{ $rows->count() }} Results</span>
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
                        <label class="form-label fw-bold">Agent</label>
                        <select name="agent_id" class="form-select">
                            <option value="all" {{ $selectedAgent === 'all' ? 'selected' : '' }}>All agents</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}"
                                {{ (string)$selectedAgent === (string)$agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All status</option>
                            <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="accepted" {{ $selectedStatus === 'accepted' ? 'selected' : '' }}>Accepted
                            </option>
                            <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="enrolled" {{ $selectedStatus === 'enrolled' ? 'selected' : '' }}>Enrolled
                            </option>
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
                                <th style="width:45px;" class="text-center">
                                    <input type="checkbox" onchange="toggleAllStudentExportCheckboxes(this)">
                                </th>
                                <th style="width:55px;">#</th>
                                <th>Student</th>
                                <th style="width:140px;">Agent</th>
                                <th style="width:260px;">Documents</th>
                                <th style="width:130px;">Photo</th>
                                <th style="width:150px;">Status</th>
                                <th style="width:220px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $index => $row)
                            @php
                            $application = $row['application'];
                            $st = $row['student'];
                            $schoolRow = $row['school'];

                            $name = $st?->student_name ?? '';
                            $jp = $st?->student_name_jp ?? '';
                            $photo = $st?->photo ?? '';
                            $agent = $st?->creator?->name ?? '-';
                            $gender = $st?->gender ?? '';
                            $nat = $st?->nationality ?? '';
                            $age = $st?->age ?? '';
                            $initial = strtoupper(mb_substr($name, 0, 1));

                            $status = $application?->status ?? 'pending';

                            $statusClass = match($status) {
                            'accepted' => 'chip-accepted',
                            'rejected' => 'chip-rejected',
                            'enrolled' => 'chip-enrolled',
                            default => 'chip-pending',
                            };

                            @endphp

                            <tr>
                                 <td class="text-center">
                                    <input type="checkbox" class="student-export-checkbox" value="{{ $st->id }}"
                                        onchange="updateSelectedStudentCount()">
                                </td>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="thumb-mini">{{ $initial ?: 'S' }}</div>
                                        <div>
                                            <div class="student-name">{{ $name }}</div>
                                            @if($jp)
                                            <div class="student-meta">{{ $jp }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $agent }}</td>

                                <td class="text-muted" style="font-size:12px;">
                                    {{ $gender ? $gender . ' • ' : '' }}
                                    {{ $nat ? $nat . ' • ' : '' }}
                                    {{ $age ? 'Age: ' . $age . ' • ' : '' }}
                                    {{ !empty($st?->intake) ? 'Intake: ' . $st->intake : '' }}
                                </td>

                                <td>
                                    @php
                                    $rawPath = trim((string)($photo ?? ''));
                                    $rawPath = str_replace('\\', '/', $rawPath);
                                    $rawPath = preg_replace('#^/?storage/#', '', $rawPath);
                                    $rawPath = ltrim($rawPath, '/');

                                    $fileUrl = asset('storage/' . $rawPath);
                                    @endphp
                                    @if($fileUrl)
                                    <img src="{{ $fileUrl }}" alt="Student Photo" class="thumb">
                                    @else
                                    <span class="text-muted">No photo</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="status-chip {{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a class="btn btn-sm btn-success"
                                            href="{{ route('student.file.show', [$st, $school]) }}">
                                            View
                                        </a>

                                        <a class="btn btn-sm btn-primary" href="{{ route('student.zip', $st) }}">
                                            ZIP Files
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No students enrolled yet.</td>
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

@if(session('error') || $errors->any())
<div id="toastMsg" class="toast-pop" style="background:#dc3545; color:#fff;">
    <div style="font-weight:900;">Error</div>
    <div>{{ session('error') ?: 'Please check the form.' }}</div>
</div>
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const successToast = document.getElementById('liveToastSuccess');
    const errorToast = document.getElementById('liveToastError');

    if (successToast) {
        new bootstrap.Toast(successToast, {
            delay: 3500
        }).show();
    }

    if (errorToast) {
        new bootstrap.Toast(errorToast, {
            delay: 4500
        }).show();
    }
});
</script>
@endsection