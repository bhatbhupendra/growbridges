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
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">← Dashboard</a>
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
                </div>

                <form method="GET" class="filter-bar row g-2 align-items-end mb-2">
                    <div class="col-md-4">
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

                    <div class="col-md-5">
                        <label class="form-label mb-1" style="font-weight:800;">School</label>
                        <select name="school_id" class="form-select">
                            <option value="all" {{ $selectedSchool === 'all' ? 'selected' : '' }}>All schools</option>
                            @foreach($schools as $school)
                            <option value="{{ $school->id }}"
                                {{ (string)$selectedSchool === (string)$school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-grid">
                        <button class="btn btn-sm btn-primary" type="submit">Apply Filter</button>
                    </div>

                    <div class="col-12">
                        <div class="text-muted" style="font-size:12px;">
                            Showing:
                            <b>{{ $selectedIntake === 'all' ? 'All intakes' : $selectedIntake }}</b> /
                            <b>{{ $selectedSchool === 'all' ? 'All schools' : 'Selected school' }}</b>
                            <a class="ms-2" href="{{ route('admin.agents.show', $agent) }}">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:55px;">#</th>
                                <th>Student</th>
                                <th style="width:170px;">School</th>
                                <th style="width:360px;">Documents</th>
                                <th style="width:180px;">Photo</th>
                                <th style="width:190px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $row)
                            @php
                                $student = $row['student'];
                                $schools = collect($row['schools']);
                                $activeSchoolId = $row['active_school_id'];
                                $activeSchool = $schools->firstWhere('id', $activeSchoolId) ?? $schools->first();
                            @endphp
                            <tr>
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
                                    @if($schools->isEmpty())
                                        <span class="text-muted">—</span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($schools as $schoolItem)
                                                <button
                                                    type="button"
                                                    class="btn btn-sm school-switch-btn {{ $activeSchool && $activeSchool['id'] == $schoolItem['id'] ? 'btn-primary' : 'btn-outline-primary' }}"
                                                    data-student-id="{{ $student->id }}"
                                                    data-school-id="{{ $schoolItem['id'] }}">
                                                    {{ $schoolItem['name'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="doc-list" id="docs-{{ $student->id }}">
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

                                <td class="text-center" id="photo-{{ $student->id }}">
                                    @if($activeSchool && !empty($activeSchool['photo_url']))
                                        <img src="{{ $activeSchool['photo_url'] }}" class="thumb" alt="Student Photo">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ $activeSchool['view_url'] ?? '#' }}"
                                        id="view-btn-{{ $student->id }}"
                                        class="btn btn-sm btn-success w-100 mb-1 {{ $activeSchool ? '' : 'disabled' }}">
                                        View Student
                                    </a>

                                    <a href="{{ route('student.zip', $student) }}" class="btn btn-sm btn-primary w-100 mb-1">
                                        ZIP FILES
                                    </a>

                                    <form method="POST" action="{{ route('student.destroy', $student) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                            onclick="return confirm('Move this student to recycle bin?')">
                                            Delete Student
                                        </button>
                                    </form>

                                    <script type="application/json" id="student-school-data-{{ $student->id }}">
                                        @json($schools->values())
                                    </script>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No students found for this filter.</td>
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
                    Filter students by <b>Intake</b> first, then narrow by <b>School</b>.
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
                    Use <b>Reset</b> to quickly show all students again.
                </div>
            </div>
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

    document.querySelectorAll('.school-switch-btn').forEach(button => {
        button.addEventListener('click', function () {
            const studentId = this.dataset.studentId;
            const schoolId = parseInt(this.dataset.schoolId, 10);

            const dataEl = document.getElementById(`student-school-data-${studentId}`);
            if (!dataEl) return;

            const schools = JSON.parse(dataEl.textContent || '[]');
            const selected = schools.find(s => parseInt(s.id, 10) === schoolId);
            if (!selected) return;

            document.querySelectorAll(`.school-switch-btn[data-student-id="${studentId}"]`).forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });

            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            const docsEl = document.getElementById(`docs-${studentId}`);
            if (docsEl) {
                if (selected.docs && selected.docs.length) {
                    docsEl.innerHTML = selected.docs.map(doc => {
                        const cls = doc.submitted ? 'doc-ok' : 'doc-miss';
                        const icon = doc.submitted ? '✔' : '✖';
                        return `<div class="${cls}">${icon} ${doc.name}</div>`;
                    }).join('');
                } else {
                    docsEl.innerHTML = `<span class="text-muted">No required documents set.</span>`;
                }
            }

            const photoEl = document.getElementById(`photo-${studentId}`);
            if (photoEl) {
                if (selected.photo_url) {
                    photoEl.innerHTML = `<img src="${selected.photo_url}" class="thumb" alt="Student Photo">`;
                } else {
                    photoEl.innerHTML = `<span class="text-muted">—</span>`;
                }
            }

            const viewBtn = document.getElementById(`view-btn-${studentId}`);
            if (viewBtn) {
                viewBtn.href = selected.view_url || '#';
                viewBtn.classList.remove('disabled');
            }
        });
    });
});
</script>
@endsection