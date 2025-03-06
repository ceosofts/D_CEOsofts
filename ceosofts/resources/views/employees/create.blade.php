@extends('layouts.app')

@section('title', $editMode ? 'Edit Employee' : 'Add Employee')

@section('content')
<div class="container">
    <h1 class="mb-4">
        <i class="bi bi-person-badge"></i>
        {{ $editMode ? 'Edit Employee' : 'Add Employee' }}
    </h1>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Display Validation Errors --}}
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

            {{-- Employee Form --}}
            <form action="{{ $editMode ? route('employees.update', $employee->id) : route('employees.store') }}" method="POST">
                @csrf
                @if($editMode)
                    @method('PUT')
                @endif

                {{-- 1. Employee Code (Auto Generated, Readonly) --}}
                <div class="mb-3">
                    <label class="form-label">Employee Code</label>
                    <input type="text" name="employee_code" class="form-control" 
                           value="{{ old('employee_code', $employee->employee_code ?? $employee_code ?? '') }}" readonly>
                </div>

                {{-- 2. First Name (Required) --}}
                <div class="mb-3">
                    <label class="form-label text-danger">First Name *</label>
                    <input type="text" name="first_name" class="form-control" 
                           value="{{ old('first_name', optional($employee)->first_name) }}" required>
                </div>

                {{-- 3. Last Name (Required) --}}
                <div class="mb-3">
                    <label class="form-label text-danger">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" 
                           value="{{ old('last_name', optional($employee)->last_name) }}" required>
                </div>

                {{-- 4. Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="{{ old('email', optional($employee)->email) }}">
                </div>

                {{-- 5. National ID --}}
                <div class="mb-3">
                    <label class="form-label">National ID</label>
                    <input type="text" name="national_id" class="form-control" 
                           value="{{ old('national_id', optional($employee)->national_id) }}">
                </div>

                {{-- 6. Driver License --}}
                <div class="mb-3">
                    <label class="form-label">Driver License</label>
                    <input type="text" name="driver_license" class="form-control" 
                           value="{{ old('driver_license', optional($employee)->driver_license) }}">
                </div>

                {{-- 7. Date of Birth --}}
                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" 
                           value="{{ old('date_of_birth', optional($employee)->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                </div>

                {{-- 8. Phone --}}
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" 
                           value="{{ old('phone', optional($employee)->phone) }}">
                </div>

                {{-- 9. Address --}}
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control">{{ old('address', optional($employee)->address) }}</textarea>
                </div>

                {{-- 10. Emergency Contact Name --}}
                <div class="mb-3">
                    <label class="form-label">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control" 
                           value="{{ old('emergency_contact_name', optional($employee)->emergency_contact_name) }}">
                </div>

                {{-- 11. Emergency Contact Phone --}}
                <div class="mb-3">
                    <label class="form-label">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" class="form-control" 
                           value="{{ old('emergency_contact_phone', optional($employee)->emergency_contact_phone) }}">
                </div>

                {{-- 12. Spouse Name --}}
                <div class="mb-3">
                    <label class="form-label">Spouse Name</label>
                    <input type="text" name="spouse_name" class="form-control" 
                           value="{{ old('spouse_name', optional($employee)->spouse_name) }}">
                </div>

                {{-- 13. Department --}}
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-control">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', optional($employee)->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 14. Position --}}
                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <select name="position_id" class="form-control">
                        <option value="">Select Position</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}"
                                {{ old('position_id', optional($employee)->position_id) == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 15. Employment Status --}}
                <div class="mb-3">
                    <label class="form-label">Employment Status</label>
                    <select name="employment_status" class="form-control">
                        @foreach(['active' => 'Active', 'resigned' => 'Resigned', 'terminated' => 'Terminated', 'on_leave' => 'On Leave'] as $key => $status)
                            <option value="{{ $key }}" {{ old('employment_status', optional($employee)->employment_status) == $key ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 16. Hire Date --}}
                <div class="mb-3">
                    <label class="form-label">Hire Date</label>
                    <input type="date" name="hire_date" class="form-control" 
                           value="{{ old('hire_date', optional($employee)->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}">
                </div>

                {{-- 17. Resignation Date --}}
                <div class="mb-3">
                    <label class="form-label">Resignation Date</label>
                    <input type="date" name="resignation_date" class="form-control" 
                           value="{{ old('resignation_date', optional($employee)->resignation_date ? $employee->resignation_date->format('Y-m-d') : '') }}">
                </div>

                {{-- 18. Salary --}}
                <div class="mb-3">
                    <label class="form-label">Salary</label>
                    <input type="number" name="salary" class="form-control" step="0.01"
                           value="{{ old('salary', optional($employee)->salary) }}">
                </div>

                {{-- 19. Tax Deductions --}}
                <div class="mb-3">
                    <label class="form-label">Tax Deductions</label>
                    <input type="number" name="tax_deductions" class="form-control" step="0.01"
                           value="{{ old('tax_deductions', optional($employee)->tax_deductions) }}">
                </div>

                {{-- Buttons --}}
                <div class="mt-4 d-flex">
                    <button type="submit" class="btn btn-success btn-lg w-50 me-2">Save Employee</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-lg w-50">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection