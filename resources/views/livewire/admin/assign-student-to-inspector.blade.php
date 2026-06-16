<div class="container py-4">
    <h4 class="fw-bold mb-3">Assign Students to Inspector</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @error('inspectorId')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    @error('selectedStudents')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="card p-3 mb-4">
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <select wire:model.live="inspectorId" class="form-select">
                    <option value="">Select Inspector</option>
                    @foreach ($inspectors as $inspector)
                        <option value="{{ $inspector->id }}">
                            {{ $inspector->name }} / {{ $inspector->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <input type="text"
                       wire:model.live.debounce.500ms="search"
                       class="form-control"
                       placeholder="Search student name, email, passport, intake">
            </div>

            <div class="col-md-3">
                <button type="button"
                        wire:click="assign"
                        wire:loading.attr="disabled"
                        class="btn btn-dark w-100">
                    Assign Selected Students
                </button>
            </div>
        </div>

        <table class="table table-sm table-bordered align-middle">
            <thead>
                <tr>
                    <th width="40">Select</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Passport</th>
                    <th>Intake</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr wire:key="student-{{ $student->id }}">
                        <td>
                            <input type="checkbox"
                                   wire:model.live="selectedStudents"
                                   value="{{ $student->id }}">
                        </td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->passport_number }}</td>
                        <td>{{ $student->intake }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card p-3">
        <h6 class="fw-bold mb-3">Current Assignments</h6>

        @forelse ($assignments as $inspectorAssignments)
            @php
                $inspector = $inspectorAssignments->first()->inspector;
            @endphp

            <div class="border rounded p-3 mb-3">
                <h6 class="fw-bold mb-3">
                    {{ $inspector?->name }} / {{ $inspector?->email }}
                </h6>

                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Assigned At</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inspectorAssignments as $assignment)
                            <tr wire:key="assignment-{{ $assignment->id }}">
                                <td>{{ $assignment->student?->student_name }}</td>
                                <td>{{ optional($assignment->assigned_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <button type="button"
                                            wire:click="removeAssignment({{ $assignment->id }})"
                                            class="btn btn-sm btn-danger">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center text-muted">
                No assignments yet.
            </div>
        @endforelse
    </div>
</div>