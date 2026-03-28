@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.page-container {
    padding: 24px;
}

.form-card {
    max-width: 600px;
    background: #fff;
    padding: 28px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
}

.form-title {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
    color: #111827;
}

.form-control,
.form-select {
    border-radius: 10px;
    padding: 11px 12px;
    border: 1px solid #d1d5db;
    font-size: 14px;
}

.form-control:focus,
.form-select:focus {
    border-color: #312682;
    box-shadow: 0 0 0 2px rgba(49, 38, 130, 0.08);
}

.btn-main {
    background: #161616;
    color: #fff;
    border-radius: 10px;
    padding: 12px;
    font-weight: 700;
    border: none;
    transition: 0.2s ease;
}

.btn-main:hover {
    background: #000;
}
</style>

<div class="container page-container">

    <div class="form-card">
        <div class="form-title">Create User</div>

        <form method="POST" action="{{ route('manage-users.store') }}">
            @csrf

            <input type="text" name="name" placeholder="Name" class="form-control mb-3" required>

            <input type="email" name="email" placeholder="Email" class="form-control mb-3" required>

            <input type="password" name="password" placeholder="Password" class="form-control mb-3" required>

            <select name="role" class="form-select mb-3">
                <option value="admin">Admin</option>
                <option value="agent">Agent</option>
                <option value="student">Student</option>
                <option value="school">School</option>
            </select>

            <button class="btn btn-main w-100">Create</button>
        </form>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection