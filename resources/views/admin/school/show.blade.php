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
    height: 120px;
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

.application-box {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 6px 8px;
    background: #fafafa;
}

.application-box.current-school {
    border-color: #c7d2fe;
    background: #eef2ff;
}

.application-school-name {
    font-size: 12px;
    font-weight: 700;
}

.application-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 700;
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
                                <th style="width:45px;" class="text-center">
                                    <input type="checkbox" onchange="toggleAllStudentExportCheckboxes(this)">
                                </th>
                                <th style="width:55px;">#</th>
                                <th>Student</th>
                                <th style="width:260px;">Documents</th>
                                <th style="width:130px;">Photo</th>
                                <th style="width:150px;">Applications</th>
                                <th style="width:220px;">Action</th>
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

                                    $initial = strtoupper(mb_substr((string) ($student->student_name ?? 'S'), 0, 1));
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
                                            <div class="thumb-mini">{{ $initial }}</div>

                                            <div class="w-100">
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

                                                    @if(!empty($student->age))
                                                        <span class="badge badge-soft me-1">Age: {{ $student->age }}</span>
                                                    @endif

                                                    @if(!empty($student->intake))
                                                        <span class="badge badge-soft me-1">Intake: {{ $student->intake }}</span>
                                                    @endif

                                                    @if($student->creator)
                                                        <span class="badge badge-soft me-1">Agent: {{ $student->creator->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
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
                                        <div class="mt-2">
                                            <div class="fw-bold mb-1" style="font-size:12px;">All Applications</div>
                                            @if($student->applications->isEmpty())
                                                <div class="text-muted" style="font-size:12px;">No applications assigned.</div>
                                            @else
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($student->applications->sortByDesc(fn($a) => (int) $a->school_id === (int) $school->id) as $studentApplication)
                                                        @php
                                                            $appStatus = strtolower($studentApplication->status ?? '');
                                                            $appChipClass = match($appStatus) {
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
                                                            $isCurrentSchool = (int) $studentApplication->school_id === (int) $school->id;
                                                        @endphp
                                                        <div class="application-box {{ $isCurrentSchool ? 'current-school' : '' }}">
                                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                                <div>
                                                                    <div class="application-school-name">
                                                                        {{ $studentApplication->school?->name ?? 'Unknown School' }}
                                                                    </div>
                                                                    @if($isCurrentSchool)
                                                                        <div class="application-label">Current school page</div>
                                                                    @endif
                                                                </div>
                                                                <span class="status-chip {{ $appChipClass }}">
                                                                    {{ strtoupper($studentApplication->status ?? 'pending') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <a href="{{ route('student.file.show', [$student, $school]) }}"
                                            class="btn btn-sm btn-primary w-100 mb-1">
                                            Open File
                                        </a>

                                        <a class="btn btn-sm btn-outline-dark w-100 mb-1"
                                            href="{{ route('student.zip', $student) }}">
                                            ZIP Files
                                        </a>

                                        @if(($row['available_schools']->count() ?? 0) > 0)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-success w-100 open-assign-school-modal"
                                                data-student-id="{{ $student->id }}"
                                                data-student-name="{{ $student->student_name }}"
                                                data-assign-url="{{ route('admin.students.assign-school', $student) }}"
                                                data-available-schools='@json($row["available_schools"]->map(fn($s) => ["id" => $s->id, "name" => $s->name])->values())'
                                            >
                                                Assign School
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary w-100" disabled>
                                                No More Schools
                                            </button>
                                        @endif
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
});
</script>
@endsection