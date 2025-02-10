@extends('layouts.app')

@section('title', $editMode ? 'Edit Employee' : 'Add Employee')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $editMode ? 'Edit Employee' : 'Add Employee' }}</h1>

    <div class="card">
        <div class="card-body">

            {{-- แสดงข้อผิดพลาด Validation --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>เกิดข้อผิดพลาดในการบันทึกข้อมูลพนักงาน:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $editMode ? route('employees.update', $employee->id ?? '') : route('employees.store') }}" method="POST">
                @csrf
                @if($editMode) @method('PUT') @endif

                {{-- 1️⃣ First Name (บังคับ) --}}
                <div class="mb-3">
                    <label class="form-label text-danger">1. First Name *</label>
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name', optional($employee)->first_name) }}" required>
                </div>

                {{-- 2️⃣ Last Name (บังคับ) --}}
                <div class="mb-3">
                    <label class="form-label text-danger">2. Last Name *</label>
                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name', optional($employee)->last_name) }}" required>
                </div>

                {{-- 3️⃣ Email --}}
                <div class="mb-3">
                    <label class="form-label">3. Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', optional($employee)->email) }}">
                </div>

                {{-- 4️⃣ National ID --}}
                <div class="mb-3">
                    <label class="form-label">4. National ID</label>
                    <input type="text" class="form-control" name="national_id" value="{{ old('national_id', optional($employee)->national_id) }}">
                </div>

                {{-- 5️⃣ Driver License --}}
                <div class="mb-3">
                    <label class="form-label">5. Driver License</label>
                    <input type="text" class="form-control" name="driver_license" value="{{ old('driver_license', optional($employee)->driver_license) }}">
                </div>

                {{-- 6️⃣ Date of Birth --}}
                <div class="mb-3">
                    <label class="form-label">6. Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', optional($employee)->date_of_birth) }}">
                </div>

                {{-- 7️⃣ Age (Auto-calculated) --}}
                <div class="mb-3">
                    <label class="form-label">7. Age (Auto-calculated)</label>
                    <input type="number" class="form-control" name="age" value="{{ old('age', optional($employee)->age) }}" readonly>
                </div>

                {{-- 8️⃣ Phone --}}
                <div class="mb-3">
                    <label class="form-label">8. Phone</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', optional($employee)->phone) }}">
                </div>

                {{-- 9️⃣ Address --}}
                <div class="mb-3">
                    <label class="form-label">9. Address</label>
                    <textarea class="form-control" name="address">{{ old('address', optional($employee)->address) }}</textarea>
                </div>

                {{-- 🔟 Emergency Contact Name --}}
                <div class="mb-3">
                    <label class="form-label">10. Emergency Contact Name</label>
                    <input type="text" class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name', optional($employee)->emergency_contact_name) }}">
                </div>

                {{-- 1️⃣1️⃣ Emergency Contact Phone --}}
                <div class="mb-3">
                    <label class="form-label">11. Emergency Contact Phone</label>
                    <input type="text" class="form-control" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', optional($employee)->emergency_contact_phone) }}">
                </div>

                {{-- 1️⃣2️⃣ Spouse Name --}}
                <div class="mb-3">
                    <label class="form-label">12. Spouse Name</label>
                    <input type="text" class="form-control" name="spouse_name" value="{{ old('spouse_name', optional($employee)->spouse_name) }}">
                </div>

                {{-- 1️⃣3️⃣ Children --}}
                {{-- <div class="mb-3">
                    <label class="form-label">13. Children (comma-separated)</label>
                
                    <input type="text" class="form-control" name="children" value="{{ old('children', optional($employee)->children ? implode(',', (array) optional($employee)->children) : '') }}">

                </div> --}}

                {{-- 1️⃣4️⃣ Tax Deductions --}}
                <div class="mb-3">
                    <label class="form-label">14. Tax Deductions</label>
                    <input type="number" class="form-control" name="tax_deductions" value="{{ old('tax_deductions', optional($employee)->tax_deductions) }}">
                </div>

                {{-- 1️⃣5️⃣ Department --}}
                <div class="mb-3">
                    <label class="form-label">15. Department</label>
                    <select class="form-control" name="department_id">
                        <option value="">เลือกแผนก</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', optional($employee)->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 1️⃣6️⃣ Position --}}
                <div class="mb-3">
                    <label class="form-label">16. Position</label>
                    <select class="form-control" name="position_id">
                        <option value="">เลือกตำแหน่ง</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id', optional($employee)->position_id) == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 1️⃣7️⃣ Employment Status --}}
                <div class="mb-3">
                    <label class="form-label">17. Employment Status</label>
                    <select class="form-control" name="employment_status">
                        @foreach(['active', 'resigned', 'terminated', 'on_leave'] as $status)
                            <option value="{{ $status }}" {{ old('employment_status', optional($employee)->employment_status) == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 1️⃣8️⃣ Hire Date --}}
                <div class="mb-3">
                    <label class="form-label">18. Hire Date</label>
                    <input type="date" class="form-control" name="hire_date" value="{{ old('hire_date', optional($employee)->hire_date) }}">
                </div>

                {{-- 1️⃣9️⃣ Resignation Date --}}
                <div class="mb-3">
                    <label class="form-label">19. Resignation Date</label>
                    <input type="date" class="form-control" name="resignation_date" value="{{ old('resignation_date', optional($employee)->resignation_date) }}">
                </div>

                <button type="submit" class="btn btn-success">Save Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
