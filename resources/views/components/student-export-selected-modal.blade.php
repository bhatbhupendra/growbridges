@php
$modalId = $modalId ?? 'studentExportSelectedModal';

$exportFields = [
'id' => 'ID',
'user_id' => 'User ID',
'created_by' => 'Created By ID',
'student_name' => 'Student Name',
'student_name_jp' => 'Student Name JP',
'email' => 'Email',
'gender' => 'Gender',
'dob' => 'Date of Birth',
'age' => 'Age',
'nationality' => 'Nationality',
'phone' => 'Phone',
'passport_number' => 'Passport Number',
'current_address' => 'Current Address',
'permanent_address' => 'Permanent Address',
'highest_qualification' => 'Highest Qualification',
'last_institution_name' => 'Last Institution Name',
'graduation_year' => 'Graduation Year',
'academic_gap_years' => 'Academic Gap Years',
'japanese_level' => 'Japanese Level',
'japanese_test_type' => 'Japanese Test Type',
'japanese_exam_score' => 'Japanese Exam Score',
'japanese_training_hours' => 'Japanese Training Hours',
'sponsor_name' => 'Sponsor Name',
'sponsor_relationship' => 'Sponsor Relationship',

'sponsor_name_1' => 'Sponsor 1 Name',
'sponsor_relationship_1' => 'Sponsor 1 Relationship',
'sponsor_occupation_1' => 'Sponsor 1 Occupation',
'sponsor_annual_income_1' => 'Sponsor 1 Annual Income',
'sponsor_savings_amount_1' => 'Sponsor 1 Savings Amount',

'sponsor_name_2' => 'Sponsor 2 Name',
'sponsor_relationship_2' => 'Sponsor 2 Relationship',
'sponsor_occupation_2' => 'Sponsor 2 Occupation',
'sponsor_annual_income_2' => 'Sponsor 2 Annual Income',
'sponsor_savings_amount_2' => 'Sponsor 2 Savings Amount',

'intake' => 'Intake',
'photo' => 'Photo',
'career_path' => 'Career Path',

'creator_name' => 'Created By Name',
'schools' => 'Assigned Schools',
'application_statuses' => 'Application Statuses',
'created_at' => 'Created At',
'updated_at' => 'Updated At',
];
@endphp
<style>
.student-export-modal-content {
    max-height: 90vh;
}

.student-export-modal-content .modal-body {
    overflow-y: auto;
}

.student-export-modal-content .modal-header,
.student-export-modal-content .modal-footer {
    position: sticky;
    background: #fff;
    z-index: 2;
}

.student-export-modal-content .modal-header {
    top: 0;
    border-bottom: 1px solid #dee2e6;
}

.student-export-modal-content .modal-footer {
    bottom: 0;
    border-top: 1px solid #dee2e6;
}

.student-export-fields-wrap {
    max-height: 52vh;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 6px;
}
</style>
<button type="button" class="btn btn-success btn-sm" onclick="openStudentExportModal('{{ $modalId }}')">
    Export Excel
</button>

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content student-export-modal-content">
            <form method="POST" action="{{ route('students.export.selected') }}" id="{{ $modalId }}Form">
                @csrf

                <div id="{{ $modalId }}StudentIdsContainer"></div>

                <div class="modal-header">
                    <h5 class="modal-title">Export Selected Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3">
                        <span id="{{ $modalId }}SelectedCount">0</span> student(s) selected
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">File Name</label>
                        <input type="text" name="file_name" class="form-control" value="students_export">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Select Fields</label>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                onclick="toggleExportFields('{{ $modalId }}', true)">Select All</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                onclick="toggleExportFields('{{ $modalId }}', false)">Clear</button>
                        </div>
                    </div>
                    <div class="student-export-fields-wrap">
                        <div class="row">
                            @foreach($exportFields as $value => $label)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input export-field-checkbox-{{ $modalId }}" type="checkbox"
                                        name="fields[]" value="{{ $value }}" id="{{ $modalId }}_{{ $value }}"
                                        {{ in_array($value, ['student_name', 'email', 'phone', 'gender', 'nationality', 'intake', 'schools']) ? 'checked' : '' }}
                                        <label class="form-check-label" for="{{ $modalId }}_{{ $value }}">
                                    {{ $label }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Download Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function getSelectedStudentIds() {
    return Array.from(document.querySelectorAll('.student-export-checkbox:checked')).map(cb => cb.value);
}

function openStudentExportModal(modalId) {
    const ids = getSelectedStudentIds();

    if (!ids.length) {
        alert('Please select at least one student.');
        return;
    }

    const container = document.getElementById(modalId + 'StudentIdsContainer');
    const countBox = document.getElementById(modalId + 'SelectedCount');

    container.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        container.appendChild(input);
    });

    countBox.textContent = ids.length;

    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}

function toggleExportFields(modalId, checked) {
    document.querySelectorAll('.export-field-checkbox-' + modalId).forEach(cb => {
        cb.checked = checked;
    });
}

function toggleAllStudentExportCheckboxes(source) {
    document.querySelectorAll('.student-export-checkbox').forEach(cb => {
        cb.checked = source.checked;
    });
    updateSelectedStudentCount();
}

function updateSelectedStudentCount() {
    const count = document.querySelectorAll('.student-export-checkbox:checked').length;
    const targets = document.querySelectorAll('.selected-student-count-label');
    targets.forEach(el => {
        el.textContent = count;
    });
}
</script>