<div class="section-title">Basic</div>
<div class="row g-2">
    <div class="col-md-4 mb-tight">
        <label class="form-label">Intake <span class="req">*</span></label>
        <select name="intake" class="form-select" required>
            <option value="">Required Intake</option>
            @foreach (['2026-04(April)', '2026-07(July)', '2026-10(October)', '2027-1(January)', '2027-04(April)', '2027-07(July)', '2027-10(October)'] as $intake)
            <option value="{{ $intake }}" {{ old('intake', $student->intake ?? '') == $intake ? 'selected' : '' }}>
                {{ $intake }}
            </option>
            @endforeach
        </select>
        @error('intake') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4 mb-tight">
        <label class="form-label">Student Name <span class="req">*</span></label>
        <input type="text" name="student_name" class="form-control"
            value="{{ old('student_name', $student->student_name ?? '') }}" required>
        @error('student_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4 mb-tight">
        <label class="form-label">Student Name (JP) <span class="req">*</span></label>
        <input type="text" name="student_name_jp" class="form-control"
            value="{{ old('student_name_jp', $student->student_name_jp ?? '') }}" required>
        @error('student_name_jp') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
            <option value="">--</option>
            @foreach (['Male', 'Female', 'Other'] as $gender)
            <option value="{{ $gender }}" {{ old('gender', $student->gender ?? '') == $gender ? 'selected' : '' }}>
                {{ $gender }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Date of Birth <span class="req">*</span></label>
        <input type="date" name="dob" id="dob" class="form-control"
            value="{{ old('dob', isset($student->dob) && $student->dob ? $student->dob->format('Y-m-d') : '') }}"
            required>
        @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-2 mb-tight">
        <label class="form-label">Age</label>
        <input type="number" id="age_display" class="form-control" value="{{ old('age', $student->age ?? '') }}"
            readonly>
    </div>

    <div class="col-md-2 mb-tight">
        <label class="form-label">Nationality</label>
        <input type="text" name="nationality" class="form-control"
            value="{{ old('nationality', $student->nationality ?? '') }}">
    </div>

    <div class="col-md-2 mb-tight">
        <label class="form-label">Permanent Address</label>
        <input type="text" name="permanent_address" class="form-control"
            value="{{ old('permanent_address', $student->permanent_address ?? '') }}">
    </div>
</div>

<div class="section-title">Education</div>
<div class="row g-2">
    <div class="col-md-4 mb-tight">
        <label class="form-label">Highest Qualification <span class="req">*</span></label>
        <input type="text" name="highest_qualification" class="form-control"
            value="{{ old('highest_qualification', $student->highest_qualification ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Last Institution Name <span class="req">*</span></label>
        <input type="text" name="last_institution_name" class="form-control"
            value="{{ old('last_institution_name', $student->last_institution_name ?? '') }}" required>
    </div>

    <div class="col-md-2 mb-tight">
        <label class="form-label">Graduation Year <span class="req">*</span></label>
        <input type="number" name="graduation_year" class="form-control"
            value="{{ old('graduation_year', $student->graduation_year ?? '') }}" required>
    </div>
</div>

<div class="section-title">Japanese</div>
<div class="row g-2">
    <div class="col-md-4 mb-tight">
        <label class="form-label">Japanese Level</label>
        <input type="text" name="japanese_level" class="form-control"
            value="{{ old('japanese_level', $student->japanese_level ?? '') }}">
    </div>

    <div class="col-md-4 mb-tight">
        <label class="form-label">Japanese Test Type</label>
        <input type="text" name="japanese_test_type" class="form-control"
            value="{{ old('japanese_test_type', $student->japanese_test_type ?? '') }}">
    </div>

    <div class="col-md-4 mb-tight">
        <label class="form-label">Japanese Exam Score</label>
        <input type="text" name="japanese_exam_score" class="form-control"
            value="{{ old('japanese_exam_score', $student->japanese_exam_score ?? '') }}">
    </div>
</div>

<div class="section-title">Sponsor / Finance</div>
<div class="row g-2">
    <div class="col-md-4 mb-tight">
        <label class="form-label">Sponsor 1 Name</label>
        <input type="text" name="sponsor_name_1" class="form-control"
            value="{{ old('sponsor_name_1', $student->sponsor_name_1 ?? '') }}">
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Sponsor 1 Relationship</label>
        <input type="text" name="sponsor_relationship_1" class="form-control"
            value="{{ old('sponsor_relationship_1', $student->sponsor_relationship_1 ?? '') }}">
    </div>

    <div class="col-md-5 mb-tight">
        <label class="form-label">Sponsor 1 Occupation</label>
        <input type="text" name="sponsor_occupation_1" class="form-control"
            value="{{ old('sponsor_occupation_1', $student->sponsor_occupation_1 ?? '') }}">
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 1 Annual Income</label>
        <input type="number" step="0.01" name="sponsor_annual_income_1" class="form-control"
            value="{{ old('sponsor_annual_income_1', $student->sponsor_annual_income_1 ?? '') }}">
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 1 Savings Amount</label>
        <input type="number" step="0.01" name="sponsor_savings_amount_1" class="form-control"
            value="{{ old('sponsor_savings_amount_1', $student->sponsor_savings_amount_1 ?? '') }}">
    </div>

    <div class="col-md-4 mb-tight">
        <label class="form-label">Sponsor 2 Name</label>
        <input type="text" name="sponsor_name_2" class="form-control"
            value="{{ old('sponsor_name_2', $student->sponsor_name_2 ?? '') }}">
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Sponsor 2 Relationship</label>
        <input type="text" name="sponsor_relationship_2" class="form-control"
            value="{{ old('sponsor_relationship_2', $student->sponsor_relationship_2 ?? '') }}">
    </div>

    <div class="col-md-5 mb-tight">
        <label class="form-label">Sponsor 2 Occupation</label>
        <input type="text" name="sponsor_occupation_2" class="form-control"
            value="{{ old('sponsor_occupation_2', $student->sponsor_occupation_2 ?? '') }}">
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 2 Annual Income</label>
        <input type="number" step="0.01" name="sponsor_annual_income_2" class="form-control"
            value="{{ old('sponsor_annual_income_2', $student->sponsor_annual_income_2 ?? '') }}">
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 2 Savings Amount</label>
        <input type="number" step="0.01" name="sponsor_savings_amount_2" class="form-control"
            value="{{ old('sponsor_savings_amount_2', $student->sponsor_savings_amount_2 ?? '') }}">
    </div>
</div>

<div class="section-title">Career</div>
<div class="row g-2">
    <div class="col-12 mb-tight">
        <label class="form-label">Career Path</label>
        <input type="text" name="career_path" class="form-control"
            value="{{ old('career_path', $student->career_path ?? '') }}">
    </div>
</div>

<div class="d-flex justify-content-end mt-3">
    <button type="submit" class="btn btn-primary btn-sm px-4">
        {{ isset($student) ? 'Update Student' : 'Save Student' }}
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dob = document.getElementById('dob');
    const age = document.getElementById('age_display');

    function calcAge(val) {
        if (!val) {
            age.value = '';
            return;
        }

        const d = new Date(val);
        if (isNaN(d.getTime())) {
            age.value = '';
            return;
        }

        const today = new Date();
        let years = today.getFullYear() - d.getFullYear();
        const m = today.getMonth() - d.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < d.getDate())) {
            years--;
        }

        age.value = years >= 0 ? years : '';
    }

    if (dob) {
        calcAge(dob.value);
        dob.addEventListener('change', function() {
            calcAge(this.value);
        });
    }
});
</script>