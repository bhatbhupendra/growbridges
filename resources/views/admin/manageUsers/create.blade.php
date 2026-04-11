<x-app-layout>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.page-container {
    padding: 24px;
}

/* NEW: layout wrapper */
.page-grid {
    display: grid;
    grid-template-columns: 600px 300px;
    gap: 20px;
    align-items: start;
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

/* NEW: note styling */
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

        <!-- FORM (UNCHANGED) -->
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

        <!-- NEW: SIDE NOTE -->
        <div class="note-card">
            <div class="note-title">Important Note</div>

            <div>
                <strong><u>Choose the correct role carefully</u></strong> while creating a user.
                <strong><u>Role cannot be changed later after creation.</u></strong>
            </div>

            <div class="mt-2">
                If a student is mistakenly created as a School or Agent, you must delete that user
                and create a new one with the correct <strong>Student</strong> role.
            </div>

            <div class="mt-2">
                Selecting <strong>Student</strong> will automatically create a student profile
                and assign the user to the default Pre-School.
            </div>

            <div class="mt-2">
                Selecting <strong>School</strong> will automatically create a school profile
                and link it to the user.
            </div>

            <div class="mt-2">
                Admin and Agent roles will only create a user account without additional profiles.
            </div>
        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>