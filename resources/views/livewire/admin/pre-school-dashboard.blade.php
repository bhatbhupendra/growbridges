<div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: #f4f6f9;
    }
    .page-container {
        max-width: 1520px;
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
        vertical-align: middle;
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
        width: 160px;
        height: 160px;
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
    }
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 4px;
        border-radius: 5px;
        font-weight: 400;
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
    .meta-list {
        color: #6b7280;
        line-height: 1.45;
    }
    .missing-list {
        color: #b91c1c;
        line-height: 1.45;
        font-size: 12px;
    }
    .progress-mini {
        height: 8px;
        border-radius: 999px;
        background: #e5e7eb;
        overflow: hidden;
    }
    .progress-mini-bar {
        height: 100%;
        background: #111827;
    }
    .loading-box {
        opacity: .65;
        pointer-events: none;
    }
    .assigned-school-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 4px 5px;
        background: #fff;
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

    <div class="container page-container small-ui" wire:loading.class="loading-box">
        <div class="row g-3">
            <div class="col-lg-10">

                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0">Pre-School Dashboard</h5>
                            <div class="text-muted" style="font-size:12px;">
                                Intake review and school assignment workflow for {{ $school->name }}
                            </div>
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
                        <div class="text-muted mt-1">Students stay in Pre-School as the master intake bucket until they move through review and assignment.</div>
                    @endif
                </div>

                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0" style="font-weight:800;">Pipeline</h6>
                        <span class="badge badge-soft">{{ $rows->count() }} Showing</span>
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
                        <h6 class="m-0" style="font-weight:800;">Students</h6>
                        <span class="badge badge-soft">{{ $rows->count() }} Results</span>
                    </div>

                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Search</label>
                            <input type="text" wire:model.live.debounce.400ms="search" class="form-control" placeholder="Name, email, passport...">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Intake</label>
                            <select wire:model.live="intake" class="form-select">
                                <option value="all">All intake</option>
                                @foreach($intakes as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Nationality</label>
                            <select wire:model.live="nationality" class="form-select">
                                <option value="all">All nationality</option>
                                @foreach($nationalities as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Agent</label>
                            <select wire:model.live="agentId" class="form-select">
                                <option value="all">All agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Application Status</label>
                            <select wire:model.live="status" class="form-select">
                                <option value="all">All status</option>
                                @foreach($allowedStatuses as $item)
                                    <option value="{{ $item }}">{{ ucwords(str_replace('-', ' ', $item)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:55px;">#</th>
                                    <th style="min-width:180px;">Student</th>
                                    <th style="width:200px;">Pipeline</th>
                                    <th style="width:240px;">Assigned Schools / Status</th>
                                    <th style="width:160px;">Photo</th>
                                    <th style="width:240px;">Pre-School Review</th>
                                    <th style="width:130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $index => $row)
                                    @php
                                        $application = $row['application'];
                                        $st = $row['student'];

                                        $name = $st?->student_name ?? '';
                                        $jp = $st?->student_name_jp ?? '';
                                        $agent = $st?->creator?->name ?? '-';
                                        $gender = $st?->gender ?? '';
                                        $nat = $st?->nationality ?? '';
                                        $age = $st?->age ?? '';
                                        $stage = $row['pipeline_stage'] ?? 'new';
                                    @endphp

                                    <tr wire:key="application-row-{{ $application->id }}">
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            <div class="student-name">{{ $name }}</div>
                                            @if($jp)
                                                <div class="student-meta">{{ $jp }}</div>
                                            @endif
                                            <div class="meta-list mt-1">
                                                {{ !empty($agent) ? 'Agent: '.$agent.' • ' : '' }}
                                                {{ $gender ? $gender . ' • ' : '' }}
                                                {{ $nat ? $nat . ' • ' : '' }}
                                                {{ $age ? 'Age: ' . $age . ' • ' : '' }}
                                                {{ !empty($st?->intake) ? 'Intake: ' . $st->intake : '' }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="pipeline-chip">Status</span>
                                            <span class="pipeline-chip {{ pipelineClass($stage) }}">
                                                {{ ucwords(str_replace('_', ' ', $stage)) }}
                                            </span>
                                            <div class="text-muted mt-1">
                                                {{ $row['assigned_real_school_count'] ?? 0 }} real school(s)
                                            </div>

                                            <div class="fw-bold">{{ $row['profile_completion_percent'] ?? 0 }}%</div>
                                            <div class="progress-mini my-1">
                                                <div class="progress-mini-bar" style="width: {{ $row['profile_completion_percent'] ?? 0 }}%;"></div>
                                            </div>

                                            @if(($row['profile_completion_percent'] ?? 0) >= 80)
                                                <span class="badge text-bg-success">Looks ready</span>
                                            @else
                                                <span class="badge text-bg-warning">Needs completion</span>
                                            @endif

                                            @if(!empty($row['missing_profile_fields']))
                                                <div class="missing-list mb-2">
                                                    {{ implode(', ', array_slice($row['missing_profile_fields'], 0, 5)) }}
                                                    @if(count($row['missing_profile_fields']) > 5)
                                                        ...
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-success fw-bold mb-2">Profile complete</div>
                                            @endif

                                            @if(!empty($st->admin_review_notes))
                                                <div class="text-muted">
                                                    <b>Note:</b> {{ $st->admin_review_notes }}
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            @if(!empty($row['assigned_schools']) && count($row['assigned_schools']))
                                                <div class="accordion" id="assignedSchoolsAccordion-{{ $application->id }}">
                                                    @foreach($row['assigned_schools'] as $assignedSchool)
                                                        @php
                                                            $schoolStatusValue = strtolower($assignedSchool['status'] ?? 'pending');

                                                            $schoolStatusClass = match($schoolStatusValue) {
                                                                'selected', 'coe-granted' => 'chip-accepted',
                                                                'rejected', 'coe-rejected', 'visa-rejected', 'withdrawal' => 'chip-rejected',
                                                                'visa-granted' => 'chip-enrolled',
                                                                default => 'chip-pending',
                                                            };
                                                        @endphp

                                                        <div class="assigned-school-card mb-2">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#assignedSchoolCollapse-{{ $assignedSchool['application_id'] }}"
                                                                aria-expanded="false"
                                                                aria-controls="assignedSchoolCollapse-{{ $assignedSchool['application_id'] }}"
                                                                style="cursor:pointer;"
                                                            >
                                                                <div class="fw-bold">
                                                                    {{ $assignedSchool['school_name'] }}
                                                                    @if($assignedSchool['is_current'])
                                                                        <span class="text-muted">(Current)</span>
                                                                    @endif
                                                                    <span>▾</span>
                                                                </div>

                                                                <span class="status-chip {{ $schoolStatusClass }}">
                                                                    {{ ucfirst($schoolStatusValue) }}
                                                                </span>
                                                            </div>

                                                            <div
                                                                id="assignedSchoolCollapse-{{ $assignedSchool['application_id'] }}"
                                                                class="accordion-collapse collapse mt-2"
                                                                data-bs-parent="#assignedSchoolsAccordion-{{ $application->id }}"
                                                            >
                                                                <select
                                                                    class="form-select form-select-sm mb-2"
                                                                    wire:model="schoolStatusInputs.{{ $assignedSchool['application_id'] }}"
                                                                    wire:key="school-status-{{ $assignedSchool['application_id'] }}"
                                                                >
                                                                    @foreach($allowedStatuses as $item)
                                                                        <option value="{{ $item }}">
                                                                            {{ ucwords(str_replace('-', ' ', $item)) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>

                                                                <div class="d-flex gap-1">
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-sm btn-outline-dark w-100"
                                                                        wire:click="saveSchoolStatus({{ $assignedSchool['application_id'] }})"
                                                                    >
                                                                        Update Status
                                                                    </button>

                                                                    @if(!$assignedSchool['is_current'])
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-sm btn-outline-danger w-100"
                                                                            wire:click="removeAssignedSchoolDirect({{ $application->id }}, {{ $assignedSchool['application_id'] }})"
                                                                        >
                                                                            Remove
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No assigned schools</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if(!empty($row['photo_url']))
                                                <img src="{{ $row['photo_url'] }}" alt="Student Photo" class="thumb">
                                            @else
                                                <span class="text-muted">No photo</span>
                                            @endif
                                        </td>

                                        <td>
                                            <select
                                                wire:model="reviewInputs.{{ $application->id }}.pre_school_status"
                                                class="form-select form-select-sm mb-1"
                                            >
                                                <option value="new">New / Unreviewed</option>
                                                <option value="incomplete">Incomplete</option>
                                                <option value="incomplete_language">Incomplete (Language)</option>
                                                <option value="ready">Ready for Assignment</option>
                                            </select>

                                            <textarea
                                                wire:model.defer="reviewInputs.{{ $application->id }}.admin_review_notes"
                                                rows="2"
                                                class="form-control form-control-sm mb-1"
                                                placeholder="Admin review note..."
                                            ></textarea>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary w-100"
                                                wire:click="saveReview({{ $application->id }})"
                                            >
                                                Update Review
                                            </button>

                                            @if(!empty($st->admin_reviewed_at))
                                                <div class="text-muted mt-1">
                                                    Reviewed: {{ \Carbon\Carbon::parse($st->admin_reviewed_at)->format('Y-m-d H:i') }}
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a class="btn btn-sm btn-success w-100 mb-1"
                                                   href="{{ route('student.file.show', [$st, $school]) }}">
                                                    View Student
                                                </a>

                                                <a class="btn btn-sm btn-primary w-100 mb-1"
                                                   href="{{ route('student.zip', $st) }}">
                                                    ZIP Files
                                                </a>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary w-100 mb-1"
                                                    wire:click="openAssignModal({{ $application->id }})"
                                                >
                                                    Assign School
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No students enrolled yet.</td>
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
                        Manage students through the Pre-School pipeline:

                        <div class="mt-2">
                            • <b>New</b> → Not reviewed<br>
                            • <b>Incomplete</b> → Missing info/docs<br>
                            • <b>Incomplete (Language)</b> → Language requirement not met<br>
                            • <b>Ready</b> → Ready for assignment<br>
                            • <b>Assigned</b> → Sent to schools<br>
                            • <b>Interview</b> → School requested interview<br>
                            • <b>Selected</b> → Accepted by school<br>
                            • <b>Rejected</b> → Reassign or review again
                        </div>

                        <div class="mt-2">
                            Use this page to track progress, assign schools, and manage decisions efficiently.
                        </div>
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

        @if(session('success'))
            <div class="toast-pop" style="background:#198754; color:#fff;">
                <div style="font-weight:900;">Success</div>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-pop" style="background:#dc3545; color:#fff;">
                <div style="font-weight:900;">Error</div>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div wire:ignore.self class="modal fade" id="assignSchoolModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h6 class="modal-title" style="font-weight:800;">Assign Student to School</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <div class="text-muted" style="font-size:12px;">Student</div>
                            <div style="font-weight:800;">{{ $assignStudentName }}</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Available Schools</label>
                            <select wire:model="assignSchoolId" class="form-select">
                                <option value="">Select school</option>
                                @foreach($assignAvailableSchools as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(empty($assignAvailableSchools))
                            <div class="alert alert-warning py-2 mb-0">
                                No more schools available for this student.
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button
                            type="button"
                            class="btn btn-sm btn-dark"
                            wire:click="assignSchool"
                            @disabled(empty($assignAvailableSchools))
                        >
                            Assign School
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('livewire:init', () => {
        const modalEl = document.getElementById('assignSchoolModal');
        const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

        Livewire.on('open-assign-school-modal', () => modal?.show());
        Livewire.on('close-assign-school-modal', () => modal?.hide());

        setTimeout(() => {
            document.querySelectorAll('.toast-pop').forEach(el => {
                setTimeout(() => el.remove(), 3500);
            });
        }, 100);
    });
    </script>
</div>