@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container">
    <h3>Edit User</h3>

    <form method="POST" action="{{ route('manage-users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <input type="text" name="name" value="{{ $user->name }}" class="form-control mb-2">
        <input type="email" name="email" value="{{ $user->email }}" class="form-control mb-2">

        <input type="password" name="password" placeholder="New Password (optional)" class="form-control mb-2">

        <select name="role" class="form-control mb-2">
            <option value="user" {{ $user->role=='user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Admin</option>
            <option value="owner" {{ $user->role=='owner' ? 'selected' : '' }}>Owner</option>
        </select>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection