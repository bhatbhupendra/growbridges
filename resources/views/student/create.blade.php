@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f4f6f9;
}

.page-container {
    max-width: 1200px;
    margin: 24px auto;
}

.card-box {
    padding: 18px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
    background: #fff;
}

.small-ui,
.small-ui * {
    font-size: 12.5px;
}

.form-label {
    margin-bottom: 4px;
    font-weight: 600;
}

.form-control,
.form-select {
    padding: .38rem .55rem;
}

.mb-tight {
    margin-bottom: 10px !important;
}

.section-title {
    font-weight: 800;
    font-size: 13px;
    margin: 10px 0 8px;
    padding-top: 6px;
    border-top: 1px dashed #ddd;
}

.req {
    color: #dc3545;
}
</style>

<div class="container page-container small-ui">
    <div class="card-box">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h5 class="m-0">Add Student (Japan)</h5>
                <div class="text-muted" style="font-size:12px;">Single form • Compact view</div>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">Back</a>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger py-2">
            Please fix the form errors.
        </div>
        @endif

        <form method="POST" action="{{ route('student.store') }}">
            @csrf
            @include('student._form')
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection