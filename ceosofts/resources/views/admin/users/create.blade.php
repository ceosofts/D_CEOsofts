@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้งานใหม่')

@push('styles')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link href="{{ asset('css/admin-forms.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container animate-fadeIn">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">เพิ่มผู้ใช้งานใหม่</h1>
            <p class="text-muted">กรอกข้อมูลด้านล่างเพื่อสร้างบัญชีผู้ใช้งานใหม่</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger animate-shake">
            <strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลที่กรอก
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                
                <!-- User Information Section -->
                <div class="form-section">
                    <h5 class="form-section-title">ข้อมูลพื้นฐาน</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                <div class="input-icon-group">
                                    <i class="bi bi-person"></i>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                                <div class="input-icon-group">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-telephone"></i>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_id" class="form-label">แผนก</label>
                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                    <option value="">-- เลือกแผนก --</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Settings Section -->
                <div class="form-section">
                    <h5 class="form-section-title">ตั้งค่าบัญชี</h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">บทบาท <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-4">
                                @foreach($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="role_{{ $role->name }}" 
                                            value="{{ $role->name }}" {{ old('role') == $role->name ? 'checked' : '' }}
                                            {{ old('role') === null && $role->name === 'user' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->name }}">
                                            {{ ucfirst($role->name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                                <div class="input-icon-group">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                </div>
                                <div class="password-strength-meter mt-2">
                                    <div class="password-strength-meter-fill"></div>
                                </div>
                                <div class="form-text">รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                <div class="input-icon-group">
                                    <i class="bi bi-lock-fill"></i>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const passwordStrengthMeter = document.querySelector('.password-strength-meter-fill');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Remove existing classes
            passwordStrengthMeter.parentElement.classList.remove(
                'password-strength-weak', 
                'password-strength-fair', 
                'password-strength-good', 
                'password-strength-strong'
            );
            
            if (password.length > 0) {
                // Length check
                if (password.length >= 8) strength += 1;
                
                // Character variety checks
                if (/[A-Z]/.test(password)) strength += 1;
                if (/[0-9]/.test(password)) strength += 1;
                if (/[^A-Za-z0-9]/.test(password)) strength += 1;
                
                // Assign class based on strength
                if (strength === 1) {
                    passwordStrengthMeter.parentElement.classList.add('password-strength-weak');
                } else if (strength === 2) {
                    passwordStrengthMeter.parentElement.classList.add('password-strength-fair');
                } else if (strength === 3) {
                    passwordStrengthMeter.parentElement.classList.add('password-strength-good');
                } else if (strength >= 4) {
                    passwordStrengthMeter.parentElement.classList.add('password-strength-strong');
                }
            }
        });
    });
</script>
@endpush