@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Student Details</h4>
        <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body small">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $student->student_name }}</p>
                    <p><strong>JP Name:</strong> {{ $student->student_name_jp }}</p>
                    <p><strong>Intake:</strong> {{ $student->intake }}</p>
                    <p><strong>Gender:</strong> {{ $student->gender }}</p>
                    <p><strong>DOB:</strong> {{ $student->dob?->format('Y-m-d') }}</p>
                    <p><strong>Age:</strong> {{ $student->age }}</p>
                    <p><strong>Nationality:</strong> {{ $student->nationality }}</p>
                    <p><strong>Permanent Address:</strong> {{ $student->permanent_address }}</p>
                </div>

                <div class="col-md-6">
                    <p><strong>Highest Qualification:</strong> {{ $student->highest_qualification }}</p>
                    <p><strong>Institution:</strong> {{ $student->last_institution_name }}</p>
                    <p><strong>Graduation Year:</strong> {{ $student->graduation_year }}</p>
                    <p><strong>Japanese Level:</strong> {{ $student->japanese_level }}</p>
                    <p><strong>Test Type:</strong> {{ $student->japanese_test_type }}</p>
                    <p><strong>Exam Score:</strong> {{ $student->japanese_exam_score }}</p>
                    <p><strong>Career Path:</strong> {{ $student->career_path }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection