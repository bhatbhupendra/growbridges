<x-app-layout>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.page-container {
    padding: 24px;
}

/* layout */
.page-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 20px;
    align-items: start;
}

/* form card */
.form-card {
    background: #fff;
    padding: 28px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
}

/* note card */
.note-card {
    background: #fffbeb;
    border: 1px solid #fcd34d;
    border-radius: 14px;
    padding: 18px;
    font-size: 13.5px;
    color: #92400e;
    position: sticky;
    top: 20px;
}

.note-title {
    font-weight: 800;
    margin-bottom: 10px;
    font-size: 14px;
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
}

.btn-main:hover {
    background: #000;
}

.readonly-role {
    background-color: #f9fafb !important;
    cursor: not-allowed;
}

/* responsive */
@media (max-width: 900px) {
    .page-grid {
        grid-template-columns: 1fr;
    }

    .note-card {
        position: relative;
        top: 0;
    }
}
</style>

<div class="container page-container">

    <div class="page-grid">

        <!-- LEFT SIDE (FORM) -->
        <div class="form-card">
            <div class="form-title">Edit User</div>

            <form method="POST" action="{{ route('manage-users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control mb-3" required>

                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control mb-3" required>

                <input type="password" name="password" placeholder="New Password (optional)" class="form-control mb-3">

                <label class="form-label fw-semibold">Role</label>
                <select class="form-select mb-3 readonly-role" disabled>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="agent" {{ $user->role == 'agent' ? 'selected' : '' }}>Agent</option>
                    <option value="school" {{ $user->role == 'school' ? 'selected' : '' }}>School</option>
                    <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Student</option>
                </select>

                <input type="hidden" name="role" value="{{ $user->role }}">

                <button class="btn btn-main w-100">Update</button>
            </form>
        </div>

        <!-- RIGHT SIDE (NOTE) -->
        <div class="note-card">
            <div class="note-title">Important Note</div>

            <div>
                <strong><u>Role cannot be changed </u></strong> after user creation.
                Please only update the user's basic information such as name, email, or password here.
            </div>

            <div class="mt-2">
                If a self-applying student was mistakenly created as a school user, do not try to change the role.
                <strong><u>Delete that user and create a new one with the correct Student</u></strong> role.
            </div>

            <div class="mt-2">
                Similarly, if any user is created with the wrong role (School, Student, Agent, or Admin),
                delete the incorrect account and recreate it with the correct role.
            </div>

            <div class="mt-2">
                <strong><u>This prevents wrong profile linkage, missing records, and data mismatch in Student and School tables.</u></strong>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>