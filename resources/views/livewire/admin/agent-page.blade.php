<div>
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

.filter-chip {
    text-decoration: none;
    border-radius: 999px;
    padding: 7px 12px;
    font-weight: 700;
    border: 1px solid #dbe1ea;
    color: #334155;
    background: #fff;
}

.filter-chip.active {
    background: #111827;
    border-color: #111827;
    color: #fff;
}

.pipeline-chip {
    display: inline-block;
    padding: 4px 9px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 800;
    border: 1px solid transparent;
}

.pipeline-new {
    background: #e0f2fe;
    color: #075985;
    border-color: #bae6fd;
}

.pipeline-incomplete {
    background: #fef3c7;
    color: #92400e;
    border-color: #fde68a;
}

.pipeline-incomplete-language {
    background: #fff7ed;
    color: #9a3412;
    border: 1px solid #fed7aa;
}

.pipeline-ready {
    background: #dcfce7;
    color: #166534;
    border-color: #bbf7d0;
}

.pipeline-assigned {
    background: #ede9fe;
    color: #5b21b6;
    border-color: #ddd6fe;
}

.pipeline-interview {
    background: #cffafe;
    color: #155e75;
    border-color: #a5f3fc;
}

.pipeline-selected {
    background: #d1fae5;
    color: #065f46;
    border-color: #a7f3d0;
}

.pipeline-rejected_all {
    background: #fee2e2;
    color: #991b1b;
    border-color: #fecaca;
}
</style>

@php
    function pipelineClass($stage) {
        return match($stage) {
            'new' => 'pipeline-new',
            'incomplete' => 'pipeline-incomplete',
            'incomplete_language' => 'pipeline-incomplete-language',
            'ready' => 'pipeline-ready',
            'assigned' => 'pipeline-assigned',
            'interview' => 'pipeline-interview',
            'selected' => 'pipeline-selected',
            'rejected_all' => 'pipeline-rejected_all',
            default => 'pipeline-new',
        };
    }
@endphp

<div class="container page-container small-ui" wire:loading.class="opacity-50">
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
                    <h6 class="m-0" style="font-weight:800;">Pipeline</h6>
                    <span class="badge badge-soft">{{ $students->count() }} Showing</span>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @foreach([
                        'all' => 'All',
                        'new' => 'New',
                        'incomplete' => 'Incomplete',
                        'incomplete_language' => 'Incomplete Language',
                        'ready' => 'Ready',
                        'assigned' => 'Assigned',
                        'interview' => 'Interview',
                        'selected' => 'Selected',
                        'rejected_all' => 'Rejected by All',
                    ] as $key => $label)
                        <button
                            type="button"
                            wire:click="setPipeline('{{ $key }}')"
                            class="filter-chip {{ $pipeline === $key ? 'active' : '' }}"
                        >
                            {{ $label }} ({{ $counts[$key] ?? 0 }})
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">Students List</h6>
                    @include('components.student-export-selected-modal', [
                        'modalId' => 'schoolStudentExportSelectedModal'
                    ])
                </div>

                <form class="filter-bar row g-2 align-items-end mb-2">
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-weight:800;">Intake</label>
                        <select wire:model.live="intake" class="form-select">
                            <option value="all">All intake</option>
                            @foreach($intakes as $intake)
                                <option value="{{ $intake }}">
                                    {{ $intake }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-weight:800;">Nationality</label>
                        <select wire:model.live="nationality" class="form-select">
                            <option value="all">All nationality</option>
                            @foreach($nationalities as $nationality)
                                <option value="{{ $nationality }}">
                                    {{ $nationality }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-weight:800;">School</label>
                        <select wire:model.live="schoolId" class="form-select">
                            <option value="all">All schools</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-weight:800;">Status</label>
                        <select wire:model.live="status" class="form-select">
                            <option value="all">All status</option>
                            <option value="interview">School want to interview</option>
                            <option value="selected">Selected</option>
                            <option value="rejected">Rejected</option>
                            <option value="coe-applied">COE Applied</option>
                            <option value="coe-granted">COE Granted</option>
                            <option value="coe-rejected">COE Rejected</option>
                            <option value="visa-granted">Visa Granted</option>
                            <option value="visa-rejected">Visa Rejected</option>
                            <option value="withdrawal">Withdrawal</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="text-muted" style="font-size:12px;">
                            Showing:
                            <b>{{ $selectedIntake === 'all' ? 'All intakes' : $selectedIntake }}</b> /
                            <b>{{ $selectedNationality === 'all' ? 'All nationalities' : $selectedNationality }}</b> /
                            <b>{{ $selectedSchool === 'all' ? 'All schools' : 'Selected school' }}</b> /
                            <b>{{ $selectedStatus === 'all' ? 'All status' : $selectedStatus }}</b>
                            <a class="ms-2" href="#" wire:click.prevent="resetFilters">Reset</a>
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
                                    $schoolsData = collect($row['schools']);
                                    $activeSchool = $row['active_school'];
                                    $stage = $row['pipeline_stage'] ?? 'new';

                                    $selectedAssigned = collect($row['assigned_schools'])
                                        ->firstWhere('application_id', (int) $row['selected_application_id']);

                                    $selectedStatusValue = strtolower($selectedAssigned['status'] ?? 'pending');

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
                                @endphp

                                <tr wire:key="student-row-{{ $student->id }}-{{ $row['selected_application_id'] }}">
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
                                            Switch Assigned School
                                            <select
                                                class="form-select form-select-sm assigned-school-dropdown"
                                                wire:model.live="selectedApplications.{{ $student->id }}"
                                            >
                                                @foreach($row['assigned_schools'] as $assignedSchool)
                                                    <option value="{{ $assignedSchool['application_id'] }}">
                                                        {{ $assignedSchool['school_name'] }}{{ $assignedSchool['is_current'] ? ' (Current)' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="doc-list">
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
                                        @if($row['photo_url'])
                                            <img src="{{ $row['photo_url'] }}" class="thumb" alt="Student Photo">
                                        @else
                                            <span class="text-muted">No Photo</span>
                                        @endif
                                    </td>

                                    <td>
                                        Pipeline Status
                                        <span class="pipeline-chip {{ pipelineClass($stage) }}">
                                            {{ ucwords(str_replace('_', ' ', $stage)) }}
                                        </span>
                                        <div wire:key="status-block-{{ $student->id }}-{{ $row['selected_application_id'] }}">
                                            Application Status
                                            <span class="status-chip {{ $selectedStatusClass }}">
                                                {{ ucfirst($selectedStatusValue) }}
                                            </span>

                                            <div class="mt-1">
                                                <select
                                                    wire:model="schoolStatusInputs.{{ $row['selected_application_id'] }}"
                                                    class="form-select form-select-sm mb-1"
                                                    required
                                                >
                                                    <option value="" disabled>
                                                        Select a Status
                                                    </option>
                                                    <option value="interview">School want to interview</option>
                                                    <option value="selected">Selected</option>
                                                    <option value="rejected">Rejected</option>
                                                    <option value="coe-applied">COE Applied</option>
                                                    <option value="coe-granted">COE Granted</option>
                                                    <option value="coe-rejected">COE Rejected</option>
                                                    <option value="visa-granted">Visa Granted</option>
                                                    <option value="visa-rejected">Visa Rejected</option>
                                                    <option value="withdrawal">Withdrawal</option>
                                                </select>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-dark w-100"
                                                    wire:click="saveSchoolStatus({{ $row['selected_application_id'] }})"
                                                >
                                                    Update Status
                                                </button>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="{{ $activeSchool['view_url'] ?? '#' }}"
                                            class="btn btn-sm btn-success w-100 mb-1 {{ $activeSchool ? '' : 'disabled' }}">
                                            View Student
                                        </a>

                                        <a href="{{ route('student.zip', $student) }}" class="btn btn-sm btn-primary w-100 mb-1">
                                            ZIP FILES
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary w-100 mb-1"
                                            wire:click="openAssignSchoolModal({{ $student->id }})"
                                        >
                                            Assign School
                                        </button>

                                        <form
                                            wire:submit.prevent="removeAssignedSchoolDirect({{ $student->id }}, {{ $row['selected_application_id'] }}, {{ $row['default_application_id'] ?? 0 }})"
                                            class="mb-1"
                                            wire:key="remove-school-form-{{ $student->id }}-{{ $row['selected_application_id'] }}"
                                        >
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger w-100"
                                                {{ !empty($selectedAssigned['is_current']) ? 'disabled' : '' }}
                                            >
                                                Remove Selected School
                                            </button>
                                        </form>

                                        <form wire:submit.prevent="deleteStudent({{ $student->id }})">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                                onclick="return confirm('Move this student to recycle bin?')">
                                                Delete Student
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No students found for this filter.</td>
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

<div wire:ignore.self class="modal fade modal-mini" id="assignSchoolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:800;">Assign Student to School</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-2">
                    <div class="text-muted" style="font-size:12px;">Student</div>
                    <div id="assignStudentName" style="font-weight:800;">{{ $assignStudentName }}</div>
                </div>

                <div class="mb-2">
                    <label class="form-label">Available Schools</label>
                    <select wire:model="assignSchoolId" id="assignSchoolSelect" class="form-select" required>
                        <option value="">Select school</option>
                        @foreach($assignAvailableSchools as $school)
                            <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="assignNoSchoolsMsg" class="alert alert-warning py-2 mb-0 {{ empty($assignAvailableSchools) ? '' : 'd-none' }}">
                    No more schools available for this student.
                </div>
            </div>

            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-outline-secondary"
                    data-bs-dismiss="modal">Cancel</button>
                <button
                    type="button"
                    class="btn btn-sm btn-dark"
                    id="assignSchoolSubmitBtn"
                    wire:click="assignSchool"
                    @disabled(empty($assignAvailableSchools))
                >
                    Assign School
                </button>
            </div>
        </div>
    </div>
</div>

<div wire:ignore.self class="modal fade modal-mini" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:900;">Edit Agent</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form wire:submit.prevent="updateAgent">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label" style="font-weight:900;">Name *</label>
                        <input type="text" wire:model.defer="editName" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" style="font-weight:900;">Email *</label>
                        <input type="email" wire:model.defer="editEmail" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-1">
                        <label class="form-label" style="font-weight:900;">New Password (optional)</label>
                        <input type="password" wire:model.defer="editPassword" class="form-control form-control-sm"
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

<div wire:ignore.self class="modal fade modal-mini" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:900;">Delete Agent</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

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
                <button
                    type="button"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('Delete this agent?') && @this.deleteAgent()"
                >
                    Delete
                </button>
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
document.addEventListener('livewire:init', () => {
    const assignSchoolModalEl = document.getElementById('assignSchoolModal');
    const editUserModalEl = document.getElementById('editUserModal');

    const assignSchoolModal = assignSchoolModalEl ? new bootstrap.Modal(assignSchoolModalEl) : null;
    const editUserModal = editUserModalEl ? new bootstrap.Modal(editUserModalEl) : null;

    Livewire.on('open-assign-school-modal', () => assignSchoolModal?.show());
    Livewire.on('close-assign-school-modal', () => assignSchoolModal?.hide());
    Livewire.on('close-edit-user-modal', () => editUserModal?.hide());

    function showToast() {
        const t = document.getElementById("toastMsg");
        if (t) {
            t.style.display = "block";
            setTimeout(() => {
                t.style.display = "none";
            }, 3500);
        }
    }

    showToast();

    Livewire.hook('morphed', () => {
        showToast();
    });
});
</script>
</div>