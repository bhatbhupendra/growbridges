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

.badge-soft {
    background: #eef2ff;
    color: #2b3a67;
    border: 1px solid #d6ddff;
    font-weight: 700;
}

.profile-photo-box {
    width: 110px;
    height: 110px;
    border-radius: 14px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: 800;
    color: #475569;
}

.info-line {
    margin-bottom: 4px;
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
                        <h5 class="m-0">Student Dashboard</h5>
                        <div class="text-muted" style="font-size:12px;">
                            Welcome, {{ $student->student_name ?: $user->name }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="row g-3 align-items-start">
                    <div class="col-md-2">
                        <div class="profile-photo-box">
                            {{ strtoupper(mb_substr($student->student_name ?: $user->name, 0, 1)) }}
                        </div>
                    </div>

                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-line"><b>Name:</b> {{ $student->student_name }}</div>
                                <div class="info-line"><b>JP Name:</b> {{ $student->student_name_jp }}</div>
                                <div class="info-line"><b>Email:</b> {{ $student->email ?: $user->email }}</div>
                                <div class="info-line"><b>Phone:</b> {{ $student->phone }}</div>
                                <div class="info-line"><b>Gender:</b> {{ $student->gender }}</div>
                                <div class="info-line"><b>Nationality:</b> {{ $student->nationality }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-line"><b>DOB:</b> {{ $student->dob?->format('Y-m-d') }}</div>
                                <div class="info-line"><b>Age:</b> {{ $student->age }}</div>
                                <div class="info-line"><b>Intake:</b> {{ $student->intake }}</div>
                                <div class="info-line"><b>Passport No:</b> {{ $student->passport_number }}</div>
                                <div class="info-line"><b>Highest Qualification:</b>
                                    {{ $student->highest_qualification }}</div>
                                <div class="info-line"><b>Japanese Level:</b> {{ $student->japanese_level }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0" style="font-weight:800;">My School Applications</h6>
                    <span class="badge badge-soft">{{ $student->applications->count() }} Applications</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>School</th>
                                <th style="width:180px;">Status</th>
                                <th style="width:180px;">Applied At</th>
                                <th style="width:180px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->applications as $index => $application)
                            @php
                            $status = strtolower($application->status ?? 'pending');
                            $chipClass = match($status) {
                            'accepted' => 'chip-accepted',
                            'rejected' => 'chip-rejected',
                            'enrolled' => 'chip-enrolled',
                            default => 'chip-pending',
                            };
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $application->school?->name ?? '—' }}</td>
                                <td>
                                    <span class="status-chip {{ $chipClass }}">
                                        {{ strtoupper($application->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>{{ $application->applied_at ? \Illuminate\Support\Carbon::parse($application->applied_at)->format('Y-m-d h:i A') : '—' }}
                                </td>
                                <td>
                                    @if($application->school)
                                    <a href="{{ route('student.file.show', [$student, $application->school]) }}"
                                        class="btn btn-sm btn-primary">
                                        Open File Page
                                    </a>
                                    @else
                                    <span class="text-muted">No school</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No school applications found.</td>
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
                    This is your student dashboard. Open a school application to upload documents, view status, and
                    check chat updates.
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Quick actions</div>
                <div class="d-grid gap-2">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm">
                        Notifications
                    </a>

                    @if($student->applications->first()?->school)
                    <a href="{{ route('student.file.show', [$student, $student->applications->first()->school]) }}"
                        class="btn btn-primary btn-sm">
                        Open Latest File Page
                    </a>
                    @endif
                </div>

                <hr class="my-3">

                <div class="mb-2" style="font-weight:800;">Notes</div>
                <ul style="padding-left:16px; line-height:1.55; margin-bottom:0;">
                    <li>Upload documents from the file page.</li>
                    <li>Approved documents become locked.</li>
                    <li>You can track each school separately.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection