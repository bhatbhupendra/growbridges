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
        <label class="form-label">Nationality<span class="req">*</span></label>
        <select name="nationality" class="form-select" required>
            <option value="">-- Select Nationality --</option>

            @foreach ([
                'NEPAL',
                'INDIA',
                'BANGLADESH',
                'SRILANKA',
                'CAMEROON',
                'TURKEY',
                'AMERICA',
                'UZBEKISTAN',
                'OTHER'
            ] as $nation)
                <option value="{{ $nation }}"
                    {{ old('nationality', $student->nationality ?? '') == $nation ? 'selected' : '' }}>
                    {{ $nation }}
                </option>
            @endforeach

        </select>
        @error('nationality') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-2 mb-tight">
        <label class="form-label">Email<span class="req">*</span></label>
        <input type="email" name="email" class="form-control"
            value="{{ old('email', $student->email ?? '') }}" required>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Phone(with country code)<span class="req">*</span></label>
        <input type="text" name="phone" class="form-control"
            value="{{ old('phone', $student->phone ?? '') }}" required>
        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Passport Number</label>
        <input type="text" name="passport_number" class="form-control"
            value="{{ old('passport_number', $student->passport_number ?? '') }}">
        @error('passport_number') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Passport Issue Date</label>
        <input type="date" name="passport_issue_date" class="form-control"
            value="{{ old('passport_issue_date', isset($student->passport_issue_date) && $student->passport_issue_date ? \Carbon\Carbon::parse($student->passport_issue_date)->format('Y-m-d') : '') }}">
        @error('passport_issue_date') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Passport Expiry Date</label>
        <input type="date" name="passport_expiry_date" class="form-control"
            value="{{ old('passport_expiry_date', isset($student->passport_expiry_date) && $student->passport_expiry_date ? \Carbon\Carbon::parse($student->passport_expiry_date)->format('Y-m-d') : '') }}">
        @error('passport_expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Current Address</label>
        <textarea name="current_address" class="form-control" rows="2">{{ old('current_address', $student->current_address ?? '') }}</textarea>
        @error('current_address') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Permanent Address</label>
        <textarea name="permanent_address" class="form-control" rows="2">{{ old('permanent_address', $student->permanent_address ?? '') }}</textarea>
        @error('permanent_address') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>

<div class="section-title">Family Information</div>
<div class="row g-2">

    <div class="col-md-3 mb-tight">
        <label class="form-label">Father Name</label>
        <input type="text" name="father_name" class="form-control"
            value="{{ old('father_name', $student->father_name ?? '') }}">
        @error('father_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Father Occupation</label>
        <input type="text" name="father_occupation" class="form-control"
            value="{{ old('father_occupation', $student->father_occupation ?? '') }}">
        @error('father_occupation') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Mother Name</label>
        <input type="text" name="mother_name" class="form-control"
            value="{{ old('mother_name', $student->mother_name ?? '') }}">
        @error('mother_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Mother Occupation</label>
        <input type="text" name="mother_occupation" class="form-control"
            value="{{ old('mother_occupation', $student->mother_occupation ?? '') }}">
        @error('mother_occupation') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Marital Status</label>
        <select name="marital_status" class="form-select">
            <option value="">-- Select --</option>
            @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                <option value="{{ $status }}"
                    {{ old('marital_status', $student->marital_status ?? '') == $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
            @endforeach
        </select>
        @error('marital_status') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

</div>

<div class="section-title">Education</div>
<div class="row g-2">
    <div class="col-md-4 mb-tight">
        <label class="form-label">Highest Qualification <span class="req">*</span></label>
        <input type="text" name="highest_qualification" class="form-control"
            value="{{ old('highest_qualification', $student->highest_qualification ?? '') }}" required>
        @error('highest_qualification') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Last Institution Name <span class="req">*</span></label>
        <input type="text" name="last_institution_name" class="form-control"
            value="{{ old('last_institution_name', $student->last_institution_name ?? '') }}" required>
        @error('last_institution_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-2 mb-tight">
        <label class="form-label">Graduation Year <span class="req">*</span></label>
        <input type="number" name="graduation_year" class="form-control"
            value="{{ old('graduation_year', $student->graduation_year ?? '') }}" required>
        @error('graduation_year') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Academic Gap Years</label>
        <input type="number" name="academic_gap_years" class="form-control"
            value="{{ old('academic_gap_years', $student->academic_gap_years ?? 0) }}">
        @error('academic_gap_years') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>

<div class="section-title">Japanese</div>
<div class="row g-2">
    <div class="col-md-3 mb-tight">
        <label class="form-label">Japanese Level</label>
        <input type="text" name="japanese_level" class="form-control"
            value="{{ old('japanese_level', $student->japanese_level ?? '') }}">
        @error('japanese_level') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Japanese Test Type</label>
        <input type="text" name="japanese_test_type" class="form-control"
            value="{{ old('japanese_test_type', $student->japanese_test_type ?? '') }}">
        @error('japanese_test_type') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Japanese Exam Score</label>
        <input type="text" name="japanese_exam_score" class="form-control"
            value="{{ old('japanese_exam_score', $student->japanese_exam_score ?? '') }}">
        @error('japanese_exam_score') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Japanese Language Studied Hours</label>
        <input type="number" name="japanese_training_hours" class="form-control"
            value="{{ old('japanese_training_hours', $student->japanese_training_hours ?? '') }}">
        @error('japanese_training_hours') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>

<div class="section-title">Sponsor / Finance</div>
<div class="row g-2">
    <div class="col-md-4 mb-tight">
        <label class="form-label">Sponsor 1 Name</label>
        <input type="text" name="sponsor_name_1" class="form-control"
            value="{{ old('sponsor_name_1', $student->sponsor_name_1 ?? '') }}">
        @error('sponsor_name_1') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Sponsor 1 Relationship</label>
        <input type="text" name="sponsor_relationship_1" class="form-control"
            value="{{ old('sponsor_relationship_1', $student->sponsor_relationship_1 ?? '') }}">
        @error('sponsor_relationship_1') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-5 mb-tight">
        <label class="form-label">Sponsor 1 Occupation</label>
        <input type="text" name="sponsor_occupation_1" class="form-control"
            value="{{ old('sponsor_occupation_1', $student->sponsor_occupation_1 ?? '') }}">
        @error('sponsor_occupation_1') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 1 Annual Income</label>
        <input type="number" step="0.01" name="sponsor_annual_income_1" class="form-control"
            value="{{ old('sponsor_annual_income_1', $student->sponsor_annual_income_1 ?? '') }}">
        @error('sponsor_annual_income_1') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 1 Savings Amount</label>
        <input type="number" step="0.01" name="sponsor_savings_amount_1" class="form-control"
            value="{{ old('sponsor_savings_amount_1', $student->sponsor_savings_amount_1 ?? '') }}">
        @error('sponsor_savings_amount_1') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4 mb-tight">
        <label class="form-label">Sponsor 2 Name</label>
        <input type="text" name="sponsor_name_2" class="form-control"
            value="{{ old('sponsor_name_2', $student->sponsor_name_2 ?? '') }}">
        @error('sponsor_name_2') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-tight">
        <label class="form-label">Sponsor 2 Relationship</label>
        <input type="text" name="sponsor_relationship_2" class="form-control"
            value="{{ old('sponsor_relationship_2', $student->sponsor_relationship_2 ?? '') }}">
        @error('sponsor_relationship_2') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-5 mb-tight">
        <label class="form-label">Sponsor 2 Occupation</label>
        <input type="text" name="sponsor_occupation_2" class="form-control"
            value="{{ old('sponsor_occupation_2', $student->sponsor_occupation_2 ?? '') }}">
        @error('sponsor_occupation_2') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 2 Annual Income</label>
        <input type="number" step="0.01" name="sponsor_annual_income_2" class="form-control"
            value="{{ old('sponsor_annual_income_2', $student->sponsor_annual_income_2 ?? '') }}">
        @error('sponsor_annual_income_2') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-tight">
        <label class="form-label">Sponsor 2 Savings Amount</label>
        <input type="number" step="0.01" name="sponsor_savings_amount_2" class="form-control"
            value="{{ old('sponsor_savings_amount_2', $student->sponsor_savings_amount_2 ?? '') }}">
        @error('sponsor_savings_amount_2') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>

<div class="section-title">Career</div>
<div class="row g-2">
    <div class="col-12 mb-tight">
        <label class="form-label">Career Path</label>
        <input type="text" name="career_path" class="form-control"
            value="{{ old('career_path', $student->career_path ?? '') }}">
        @error('career_path') <small class="text-danger">{{ $message }}</small> @enderror
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