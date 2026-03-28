@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container">
    <h3>Create User</h3>

    <form method="POST" action="{{ route('manage-users.store') }}">
        @csrf

        <input type="text" name="name" placeholder="Name" class="form-control mb-2" required>
        <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
        <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>

        <select name="role" class="form-control mb-2">
            <option value="admin">Admin</option>
            <option value="agent">Agent</option>
            <option value="student">Student</option>
        </select>

        <button class="btn btn-success">Create</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection