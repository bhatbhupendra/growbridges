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

.small {
    font-size: 12px;
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
    margin-bottom: 6px;
    font-size: 14px;
    line-height: 1.3;
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

.card-box {
    background: #fff;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    margin-bottom: 20px;
}

.card-header-custom {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

.info-section-title {
    font-size: 15px;
    font-weight: 700;
    text-decoration: underline;
    margin-bottom: 8px;
    color: #1f2937;
}

.info-line {
    font-size: 11px;
    line-height: 1.1;
    margin-bottom: 2px;
    color: #111827;
    word-break: break-word;
}

.info-line span {
    font-weight: 700;
}

.photo-box {
    width: 170px;
    height: 170px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    overflow: hidden;
    background: #f9fafb;
    display: flex;
    align-items: center;
    justify-content: center;
}

.student-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-fallback {
    width: 100%;
    height: 100%;
    font-size: 46px;
    font-weight: 700;
    color: #4b5563;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e5e7eb;
}

@media (max-width: 991px) {
    .photo-box {
        width: 140px;
        height: 140px;
    }
}
</style>

<div class="container page-container small-ui">
    <div class="row g-3">
        <div class="col-lg-9">

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0">Student Page</h5>
                        <div class="text-muted" style="font-size:12px;">
                            This is student page of, {{ $student->student_name ?: "$user->name" }}
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-soft">Admin View</span>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">← Dashboard</a>                        
                    </div>
                </div>
            </div>
            <div class="card-box">
                <div class="card-header-custom">Info</div>

                @php
                    $rawPath = trim((string) ($student->photo ?? ''));
                    $rawPath = str_replace('\\', '/', $rawPath);
                    $rawPath = preg_replace('#^/?storage/#', '', $rawPath);
                    $rawPath = ltrim($rawPath, '/');
                    $fileUrl = $rawPath ? asset('storage/' . $rawPath) : null;

                    $studentInitial = strtoupper(mb_substr($student->student_name ?: ($user->name ?? 'S'), 0, 1));
                @endphp

                <div class="row g-3 align-items-start">

                    <!-- Column 1 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="info-section-title">Personal Information</div>

                        <div class="info-line"><span>Name:</span> {{ $student->student_name ?? '-' }}</div>
                        <div class="info-line"><span>JP Name:</span> {{ $student->student_name_jp ?? '-' }}</div>
                        <div class="info-line"><span>Gender:</span> {{ $student->gender ?? '-' }}</div>
                        <div class="info-line"><span>DOB:</span> {{ $student->dob?->format('Y-m-d') ?? '-' }}</div>
                        <div class="info-line"><span>Age:</span> {{ $student->age ?? '-' }}</div>
                        <div class="info-line"><span>Nationality:</span> {{ $student->nationality ?? '-' }}</div>
                        <div class="info-line"><span>Intake:</span> {{ $student->intake ?? '-' }}</div>
                        <div class="info-line"><span>Email:</span> {{ $student->email ?: ($user->email ?? '-') }}</div>
                        <div class="info-line"><span>Phone:</span> {{ $student->phone ?? '-' }}</div>
                        <div class="info-line"><span>Current Address:</span> {{ $student->current_address ?? '-' }}</div>
                        <div class="info-line"><span>Permanent Address:</span> {{ $student->permanent_address ?? '-' }}</div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="info-section-title">Academics Information</div>

                        <div class="info-line"><span>Highest Qualification:</span> {{ $student->highest_qualification ?? '-' }}</div>
                        <div class="info-line"><span>Last Institution:</span> {{ $student->last_institution_name ?? '-' }}</div>
                        <div class="info-line"><span>Graduate Year:</span> {{ $student->graduation_year ?? '-' }}</div>
                        <div class="info-line"><span>Academic Gap:</span> {{ $student->academic_gap_years ?? '-' }}</div>

                        <div class="info-section-title mt-2">Japanese Language Information</div>

                        <div class="info-line"><span>Level:</span> {{ $student->japanese_level ?? '-' }}</div>
                        <div class="info-line"><span>Test Type:</span> {{ $student->japanese_test_type ?? '-' }}</div>
                        <div class="info-line"><span>Exam Score:</span> {{ $student->japanese_exam_score ?? '-' }}</div>
                        <div class="info-line"><span>Training Hours:</span> {{ $student->japanese_training_hours ?? '-' }}</div>

                        <div class="info-section-title mt-2">Passport Information</div>

                        <div class="info-line"><span>Number:</span> {{ $student->passport_number ?? '-' }}</div>
                    </div>

                    <!-- Column 3 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="info-section-title">Sponsor Main Information</div>

                        <div class="info-line"><span>Name:</span> {{ $student->sponsor_name ?? '-' }}</div>
                        <div class="info-line"><span>Relationship:</span> {{ $student->sponsor_relationship ?? '-' }}</div>

                        <div class="info-section-title mt-2">Sponsor 1 Information</div>

                        <div class="info-line"><span>Name:</span> {{ $student->sponsor_name_1 ?? '-' }}</div>
                        <div class="info-line"><span>Relationship:</span> {{ $student->sponsor_relationship_1 ?? '-' }}</div>
                        <div class="info-line"><span>Occupation:</span> {{ $student->sponsor_occupation_1 ?? '-' }}</div>
                        <div class="info-line"><span>Annual Income:</span> {{ $student->sponsor_annual_income_1 ?? '-' }}</div>
                        <div class="info-line"><span>Saving Amount:</span> {{ $student->sponsor_savings_amount_1 ?? '-' }}</div>

                        <div class="info-section-title mt-2">Sponsor 2 Information</div>

                        <div class="info-line"><span>Name:</span> {{ $student->sponsor_name_2 ?? '-' }}</div>
                        <div class="info-line"><span>Relationship:</span> {{ $student->sponsor_relationship_2 ?? '-' }}</div>
                        <div class="info-line"><span>Occupation:</span> {{ $student->sponsor_occupation_2 ?? '-' }}</div>
                        <div class="info-line"><span>Annual Income:</span> {{ $student->sponsor_annual_income_2 ?? '-' }}</div>
                        <div class="info-line"><span>Saving Amount:</span> {{ $student->sponsor_savings_amount_2 ?? '-' }}</div>
                    </div>

                    <!-- Column 4 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="info-section-title">Photo</div>

                        <div class="photo-box">
                            @if($student->photo && $fileUrl)
                                <img src="{{ $fileUrl }}" alt="Student Photo" class="student-photo">
                            @else
                                <div class="photo-fallback">{{ $studentInitial }}</div>
                            @endif
                        </div>

                        <div class="info-section-title mt-3">Other Information</div>

                        <div class="info-line"><span>Career Path:</span> {{ $student->career_path ?? '-' }}</div>
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