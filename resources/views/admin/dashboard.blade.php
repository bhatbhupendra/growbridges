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
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    background: #ffffff;
    margin-bottom: 16px;
}

.side-box {
    position: sticky;
    top: 16px;
}

.table thead th {
    white-space: nowrap;
}

.badge-soft {
    background: #eef2ff;
    color: #2b3a67;
    border: 1px solid #d6ddff;
    font-weight: 700;
}
</style>

<div class="container page-container small-ui">
    <div class="row g-3">

        <div class="col-lg-9">

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0">Admin Dashboard</h5>
                        <div class="text-muted" style="font-size:12px;">Manage agents and schools</div>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">AGENTS & CONSULTANCY</h6>
                    <span class="badge badge-soft">Users (Role: Agent)</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th style="width:120px;">Role</th>
                                <th style="width:180px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agents as $index => $agent)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $agent->name }}</td>
                                <td>{{ $agent->email }}</td>
                                <td>{{ $agent->role }}</td>
                                <td>
                                    <a class="btn btn-sm btn-success w-100"
                                        href="{{ route('admin.agents.show', $agent) }}">
                                        View User Data
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">Self-Apply Students</h6>
                    <span class="badge badge-soft">Users (Role: Student)</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th style="width:120px;">Role</th>
                                <th style="width:180px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->role }}</td>
                                <td>
                                    <a class="btn btn-sm btn-success w-100" href="{{ route('admin.student.show',$student) }}">
                                        View Student Data
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">CURRENT SCHOOLS</h6>
                    <a href="{{ route('admin.school-requirements.index') }}" class="btn btn-outline-primary btn-sm">
                        Manage Requirements
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>School Name</th>
                                <th style="width:320px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schools as $index => $school)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $school->name }}</td>
                                <td>
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ route('admin.school-requirements.index', ['school_id' => $school->id]) }}">
                                        Manage Requirements
                                    </a>

                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.school.show', $school) }}">
                                        View School
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No schools found.</td>
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
                    This admin dashboard lets you manage <b>agents</b> and <b>schools</b>.
                    Schools are connected to document requirements used in student checklists.
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Quick actions</div>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.preschool.show', 1) }}" class="btn btn-outline-primary btn-sm">
                        PRE-SCHOOL
                    </a>

                    <a href="{{ route('manage-users.index') }}" class="btn btn-outline-primary btn-sm">
                        Manage Users
                    </a>

                    <a href="" class="btn btn-outline-primary btn-sm">
                        RECYCLE BIN
                    </a>

                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm">
                        NOTIFICATIONS / ACTIVITIES
                    </a>

                    <a href="{{ url('/announcements') }}" class="btn btn-outline-primary btn-sm">
                        Announcements
                    </a>

                    <a href="{{ route('admin.school-requirements.index') }}" class="btn btn-outline-primary btn-sm">
                        Configure School Requirements
                    </a>
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">How it works</div>
                <ul style="padding-left:16px; line-height:1.55;" class="mb-0">
                    <li><b>Add School</b> here.</li>
                    <li>Go to <b>Manage Requirements</b> for each school.</li>
                    <li>Agents create students and apply them to schools.</li>
                    <li>Student checklist is generated from school requirements.</li>
                </ul>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Tips</div>
                <div class="p-2 rounded" style="background:#f8fafc; border:1px solid #e5e7eb;">
                    Keep document types organized by category (Identity, Education, Finance, etc.)
                </div>
            </div>
        </div>

    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    @if(session('success'))
    <div id="liveToastSuccess" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div id="liveToastError" class="toast align-items-center text-bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('error') ?: 'Please check the form.' }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>
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