@extends('layouts.app')

@section('content')
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
    width: 100px;
    height: 120px;
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

<div class="container page-container small-ui">
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
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'all', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'all' ? 'active' : '' }}">
                        All ({{ $counts['all'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'new', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'new' ? 'active' : '' }}">
                        New ({{ $counts['new'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'incomplete', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'incomplete' ? 'active' : '' }}">
                        Incomplete ({{ $counts['incomplete'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'incomplete_language', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                        class="filter-chip {{ $selectedPipeline === 'incomplete_language' ? 'active' : '' }}">
                        Incomplete Language ({{ $counts['incomplete_language'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'ready', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'ready' ? 'active' : '' }}">
                        Ready ({{ $counts['ready'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'assigned', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'assigned' ? 'active' : '' }}">
                        Assigned ({{ $counts['assigned'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'interview', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'interview' ? 'active' : '' }}">
                        Interview ({{ $counts['interview'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'selected', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'selected' ? 'active' : '' }}">
                        Selected ({{ $counts['selected'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.preschool.show', ['school' => $school->id, 'pipeline' => 'rejected_all', 'intake' => $selectedIntake, 'agent_id' => $selectedAgent, 'status' => $selectedStatus, 'nationality' => $selectedNationality, 'search' => $search]) }}"
                       class="filter-chip {{ $selectedPipeline === 'rejected_all' ? 'active' : '' }}">
                        Rejected by All ({{ $counts['rejected_all'] ?? 0 }})
                    </a>
                </div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">Students</h6>
                    <span class="badge badge-soft">{{ $rows->count() }} Results</span>
                </div>

                <form method="GET" class="row g-2 align-items-end mb-3">
                    <input type="hidden" name="pipeline" value="{{ $selectedPipeline }}">

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Search</label>
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Name, email, passport...">
                    </div>

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

                    <div class="col-md-2">
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
                                <option value="{{ $agent->id }}" {{ (string)$selectedAgent === (string)$agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">App Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All status</option>
                            <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="interview" {{ $selectedStatus === 'interview' ? 'selected' : '' }}>School want to interview</option>
                            <option value="selected" {{ $selectedStatus === 'selected' ? 'selected' : '' }}>Selected</option>
                            <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="coe-applied" {{ $selectedStatus === 'coe-applied' ? 'selected' : '' }}>COE Applied</option>
                            <option value="coe-granted" {{ $selectedStatus === 'coe-granted' ? 'selected' : '' }}>COE Granted</option>
                            <option value="coe-rejected" {{ $selectedStatus === 'coe-rejected' ? 'selected' : '' }}>COE Rejected</option>
                            <option value="visa-applied" {{ $selectedStatus === 'visa-applied' ? 'selected' : '' }}>Visa Applied</option>
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
                                <th style="width:55px;">#</th>
                                <th style="min-width:180px;">Student</th>
                                <th style="width:130px;">Pipeline</th>
                                <th style="width:220px;">Assigned Schools</th>
                                <th style="width:130px;">Photo</th>
                                <th style="width:170px;">School Status</th>
                                <th style="width:240px;">Pre-School Review</th>
                                <th style="width:230px;">Action</th>
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

                                    $currentAssigned = collect($row['assigned_schools'])->firstWhere('is_current', true)
                                        ?? collect($row['assigned_schools'])->first();

                                    $selectedStatusValue = strtolower($currentAssigned['status'] ?? 'pending');

                                    $selectedStatusClass = match($selectedStatusValue) {
                                        'selected', 'coe-granted' => 'chip-accepted',
                                        'rejected', 'coe-rejected', 'visa-rejected', 'withdrawal' => 'chip-rejected',
                                        'visa-granted' => 'chip-enrolled',
                                        default => 'chip-pending',
                                    };

                                    $reviewStatus = $st->pre_school_status ?? 'new';
                                @endphp

                                <tr>
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
                                        
                                        <!-- profile compleated status -->
                                        <div class="fw-bold">{{ $row['profile_completion_percent'] ?? 0 }}%</div>
                                        <div class="progress-mini my-1">
                                            <div class="progress-mini-bar" style="width: {{ $row['profile_completion_percent'] ?? 0 }}%;"></div>
                                        </div>
                                        @if(($row['profile_completion_percent'] ?? 0) >= 80)
                                            <span class="badge text-bg-success">Looks ready</span>
                                        @else
                                            <span class="badge text-bg-warning">Needs completion</span>
                                        @endif

                                        <!-- missing fields -->
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
                                            <select class="form-select form-select-sm assigned-school-dropdown" data-row="{{ $index }}">
                                                @foreach($row['assigned_schools'] as $assignedSchool)
                                                    <option
                                                        value="{{ $assignedSchool['application_id'] }}"
                                                        data-status="{{ strtolower($assignedSchool['status'] ?? 'pending') }}"
                                                        data-is-current="{{ $assignedSchool['is_current'] ? '1' : '0' }}"
                                                        data-remove-url="{{ route('preschool.remove-student-school', [$school, $application, $assignedSchool['application_id']]) }}"
                                                        data-status-url="{{ route('pre-school.applications.status', $assignedSchool['application_id']) }}"
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

                                    <td>
                                        @if(!empty($row['photo_url']))
                                            <img src="{{ $row['photo_url'] }}" alt="Student Photo" class="thumb">
                                        @else
                                            <span class="text-muted">No photo</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="status-chip {{ $selectedStatusClass }}" id="status-chip-{{ $index }}">
                                            {{ ucfirst($selectedStatusValue) }}
                                        </span>

                                        <form method="POST"
                                              action="{{ $currentAssigned ? route('pre-school.applications.status', $currentAssigned['application_id']) : '#' }}"
                                              id="status-form-{{ $index }}"
                                              class="mt-2">
                                            @csrf

                                            <select name="status" class="form-select form-select-sm mb-1" id="status-select-{{ $index }}" required>
                                                <option value="" disabled {{ empty($selectedStatusValue) ? 'selected' : '' }}>Select a Status</option>
                                                <option value="pending" {{ $selectedStatusValue === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="interview" {{ $selectedStatusValue === 'interview' ? 'selected' : '' }}>School want to interview</option>
                                                <option value="selected" {{ $selectedStatusValue === 'selected' ? 'selected' : '' }}>Selected</option>
                                                <option value="rejected" {{ $selectedStatusValue === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                <option value="coe-applied" {{ $selectedStatusValue === 'coe-applied' ? 'selected' : '' }}>COE Applied</option>
                                                <option value="coe-granted" {{ $selectedStatusValue === 'coe-granted' ? 'selected' : '' }}>COE Granted</option>
                                                <option value="coe-rejected" {{ $selectedStatusValue === 'coe-rejected' ? 'selected' : '' }}>COE Rejected</option>
                                                <option value="visa-applied" {{ $selectedStatusValue === 'visa-applied' ? 'selected' : '' }}>Visa Applied</option>
                                                <option value="visa-granted" {{ $selectedStatusValue === 'visa-granted' ? 'selected' : '' }}>Visa Granted</option>
                                                <option value="visa-rejected" {{ $selectedStatusValue === 'visa-rejected' ? 'selected' : '' }}>Visa Rejected</option>
                                                <option value="withdrawal" {{ $selectedStatusValue === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                                            </select>

                                            <button class="btn btn-sm btn-outline-dark w-100">Update Status</button>
                                        </form>
                                    </td>

                                    <td>
                                        <form method="POST" action="{{ route('preschool.review.update', $application) }}" class="mb-2">
                                            @csrf

                                            <select name="pre_school_status" class="form-select form-select-sm mb-1" required>
                                                <option value="new" {{ $reviewStatus === 'new' ? 'selected' : '' }}>New / Unreviewed</option>
                                                <option value="incomplete" {{ $reviewStatus === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                                                <option value="incomplete_language" {{ $reviewStatus === 'incomplete_language' ? 'selected' : '' }}>Incomplete (Language)</option>
                                                <option value="ready" {{ $reviewStatus === 'ready' ? 'selected' : '' }}>Ready for Assignment</option>
                                            </select>

                                            <textarea name="admin_review_notes"
                                                      rows="2"
                                                      class="form-control form-control-sm mb-1"
                                                      placeholder="Admin review note...">{{ $st->admin_review_notes }}</textarea>

                                            <button class="btn btn-sm btn-outline-primary w-100">Update Review</button>
                                        </form>

                                        @if(!empty($st->admin_reviewed_at))
                                            <div class="text-muted">
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

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary w-100 mb-1 btnAssignSchool"
                                                    data-student-name="{{ $st->student_name }}"
                                                    data-route="{{ route('preschool.assign-student-school', [$school, $application]) }}"
                                                    data-schools='@json($row["available_schools"]->map(fn($s) => ["id" => $s->id, "name" => $s->name])->values())'>
                                                Assign School
                                            </button>

                                            @php
                                                $initialRemoveUrl = $currentAssigned
                                                    ? route('preschool.remove-student-school', [$school, $application, $currentAssigned['application_id']])
                                                    : '';

                                                $initialDisabled = !$currentAssigned || $currentAssigned['is_current'];
                                            @endphp

                                            <form method="POST"
                                                  id="remove-school-form-{{ $index }}"
                                                  action="{{ $initialRemoveUrl }}"
                                                  onsubmit="return confirm('Remove this assigned school from the student?');"
                                                  class="d-inline-block w-100 mb-1">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger w-100"
                                                        id="remove-school-btn-{{ $index }}"
                                                        {{ $initialDisabled ? 'disabled' : '' }}>
                                                    Remove Selected School
                                                </button>
                                            </form>
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
</div>

@if(session('success'))
    <div id="toastMsg" class="toast-pop" style="background:#198754; color:#fff;">
        <div style="font-weight:900;">Success</div>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error') || $errors->any())
    <div id="toastError" class="toast-pop" style="background:#dc3545; color:#fff;">
        <div style="font-weight:900;">Error</div>
        <div>{{ session('error') ?: 'Please check the form.' }}</div>
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
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark" id="assignSchoolSubmitBtn">Assign School</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toast = document.getElementById('toastMsg');
    const toastError = document.getElementById('toastError');

    if (toast) {
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 3500);
    }

    if (toastError) {
        toastError.style.display = 'block';
        setTimeout(() => toastError.style.display = 'none', 4500);
    }

    const assignModalEl = document.getElementById('assignSchoolModal');
    const assignModal = assignModalEl ? new bootstrap.Modal(assignModalEl) : null;
    const assignForm = document.getElementById('assignSchoolForm');
    const assignStudentName = document.getElementById('assignStudentName');
    const assignSchoolSelect = document.getElementById('assignSchoolSelect');
    const assignNoSchoolsMsg = document.getElementById('assignNoSchoolsMsg');
    const assignSubmitBtn = document.getElementById('assignSchoolSubmitBtn');

    document.querySelectorAll('.btnAssignSchool').forEach(btn => {
        btn.addEventListener('click', function () {
            const studentName = this.dataset.studentName || 'Student';
            const route = this.dataset.route || '#';
            let schools = [];

            try {
                schools = JSON.parse(this.dataset.schools || '[]');
            } catch (e) {
                schools = [];
            }

            assignStudentName.textContent = studentName;
            assignForm.action = route;
            assignSchoolSelect.innerHTML = '<option value="">Select school</option>';

            if (schools.length === 0) {
                assignNoSchoolsMsg.classList.remove('d-none');
                assignSchoolSelect.disabled = true;
                assignSubmitBtn.disabled = true;
            } else {
                assignNoSchoolsMsg.classList.add('d-none');
                assignSchoolSelect.disabled = false;
                assignSubmitBtn.disabled = false;

                schools.forEach(school => {
                    const option = document.createElement('option');
                    option.value = school.id;
                    option.textContent = school.name;
                    assignSchoolSelect.appendChild(option);
                });
            }

            assignModal?.show();
        });
    });

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