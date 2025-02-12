@extends('layouts.app')

@section('title', $editMode ? 'Edit Employee' : 'Add Employee')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="bi bi-person-badge"></i> {{ $editMode ? 'Edit Employee' : 'Add Employee' }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- ✅ แสดง Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>เกิดข้อผิดพลาด:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ✅ ฟอร์มบันทึกข้อมูลพนักงาน --}}
            <form action="{{ $editMode ? route('employees.update', $employee->id ?? '') : route('employees.store') }}" method="POST">
                @csrf
                @if($editMode) @method('PUT') @endif

                {{-- 1️⃣ Employee Code (Auto Generated) --}}
                <div class="mb-3">
                    <label class="form-label">Employee Code</label>
                    <input type="text" class="form-control" name="employee_code"
                           value="{{ old('employee_code', $employee_code ?? '') }}" readonly>
                </div>

                {{-- 2️⃣ First Name (Required) --}}
                <div class="mb-3">
                    <label class="form-label text-danger">First Name *</label>
                    <input type="text" class="form-control" name="first_name"
                           value="{{ old('first_name', optional($employee)->first_name) }}" required>
                </div>

                {{-- 3️⃣ Last Name (Required) --}}
                <div class="mb-3">
                    <label class="form-label text-danger">Last Name *</label>
                    <input type="text" class="form-control" name="last_name"
                           value="{{ old('last_name', optional($employee)->last_name) }}" required>
                </div>

                {{-- 4️⃣ Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email"
                           value="{{ old('email', optional($employee)->email) }}">
                </div>

                {{-- 5️⃣ National ID --}}
                <div class="mb-3">
                    <label class="form-label">National ID</label>
                    <input type="text" class="form-control" name="national_id"
                           value="{{ old('national_id', optional($employee)->national_id) }}">
                </div>

                {{-- 6️⃣ Driver License --}}
                <div class="mb-3">
                    <label class="form-label">Driver License</label>
                    <input type="text" class="form-control" name="driver_license"
                           value="{{ old('driver_license', optional($employee)->driver_license) }}">
                </div>

                {{-- 7️⃣ Date of Birth --}}
                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth"
                           value="{{ old('date_of_birth', optional($employee)->date_of_birth) }}">
                </div>

                {{-- 8️⃣ Phone --}}
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone"
                           value="{{ old('phone', optional($employee)->phone) }}">
                </div>

                {{-- 9️⃣ Address --}}
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address">{{ old('address', optional($employee)->address) }}</textarea>
                </div>

                {{-- 🔟 Emergency Contact Name --}}
                <div class="mb-3">
                    <label class="form-label">Emergency Contact Name</label>
                    <input type="text" class="form-control" name="emergency_contact_name"
                           value="{{ old('emergency_contact_name', optional($employee)->emergency_contact_name) }}">
                </div>

                {{-- 11️⃣ Emergency Contact Phone --}}
                <div class="mb-3">
                    <label class="form-label">Emergency Contact Phone</label>
                    <input type="text" class="form-control" name="emergency_contact_phone"
                           value="{{ old('emergency_contact_phone', optional($employee)->emergency_contact_phone) }}">
                </div>

                {{-- 12️⃣ Spouse Name --}}
                <div class="mb-3">
                    <label class="form-label">Spouse Name</label>
                    <input type="text" class="form-control" name="spouse_name"
                           value="{{ old('spouse_name', optional($employee)->spouse_name) }}">
                </div>

                {{-- 13️⃣ Department --}}
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select class="form-control" name="department_id">
                        <option value="">เลือกแผนก</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', optional($employee)->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 14️⃣ Position --}}
                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <select class="form-control" name="position_id">
                        <option value="">เลือกตำแหน่ง</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}"
                                {{ old('position_id', optional($employee)->position_id) == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 15️⃣ Employment Status --}}
                <div class="mb-3">
                    <label class="form-label">Employment Status</label>
                    <select class="form-control" name="employment_status">
                        @foreach(['active' => 'Active', 'resigned' => 'Resigned', 'terminated' => 'Terminated', 'on_leave' => 'On Leave'] as $key => $status)
                            <option value="{{ $key }}" {{ old('employment_status', optional($employee)->employment_status) == $key ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 16️⃣ Hire Date --}}
                <div class="mb-3">
                    <label class="form-label">Hire Date</label>
                    <input type="date" class="form-control" name="hire_date"
                           value="{{ old('hire_date', optional($employee)->hire_date) }}">
                </div>

                {{-- 17️⃣ Resignation Date --}}
                <div class="mb-3">
                    <label class="form-label">Resignation Date</label>
                    <input type="date" class="form-control" name="resignation_date"
                           value="{{ old('resignation_date', optional($employee)->resignation_date) }}">
                </div>

                {{-- 18️⃣ Salary --}}
                <div class="mb-3">
                    <label class="form-label">Salary</label>
                    <input type="number" class="form-control" name="salary"
                           value="{{ old('salary', optional($employee)->salary) }}">
                </div>

                {{-- 19️⃣ Tax Deductions --}}
                <div class="mb-3">
                    <label class="form-label">Tax Deductions</label>
                    <input type="number" class="form-control" name="tax_deductions"
                           value="{{ old('tax_deductions', optional($employee)->tax_deductions) }}">
                </div>

                {{-- ✅ ปุ่มบันทึกและยกเลิก --}}
                <div class="mt-4 d-flex">
                    <button type="submit" class="btn btn-success btn-lg w-50 me-2">Save Employee</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-lg w-50">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
