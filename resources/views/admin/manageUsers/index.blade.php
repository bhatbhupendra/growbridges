@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.page-container {
    padding: 24px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-title {
    font-size: 22px;
    font-weight: 800;
    color: #111827;
}

.btn-main {
    background: #161616;
    color: #fff;
    border-radius: 10px;
    padding: 10px 16px;
    font-weight: 600;
    border: none;
}

.btn-main:hover {
    background: #000;
}

.card-box {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.table {
    margin: 0;
}

.table thead {
    background: #f9fafb;
}

.table th {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #6b7280;
}

.table td {
    vertical-align: middle;
    font-size: 14px;
}

.badge-role {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    background: #eef2ff;
    color: #312682;
}

.btn-sm {
    border-radius: 8px;
    font-size: 13px;
    padding: 6px 10px;
}

.btn-edit {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
}

.btn-edit:hover {
    background: #e5e7eb;
}

.btn-delete {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #b91c1c;
}

.btn-delete:hover {
    background: #fecaca;
}

.alert {
    border-radius: 10px;
    font-size: 14px;
}
</style>

<div class="container page-container">

    ```
    <div class="page-header">
        <div class="page-title">User Management</div>
        <a href="{{ route('manage-users.create') }}" class="btn btn-main">+ Add User</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-3">
        {{ session('success') }}
    </div>
    @endif

    <div class="card-box">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th style="width:60px;">SN</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge-role">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('manage-users.edit', $user->id) }}" class="btn btn-sm btn-edit">Edit</a>
                        <form action="{{ route('manage-users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-delete"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    ```

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection