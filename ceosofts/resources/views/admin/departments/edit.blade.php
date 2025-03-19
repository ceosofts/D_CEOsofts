@extends('layouts.app')

@section('title', 'แก้ไขแผนก')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">แก้ไขแผนก</h1>
            <p class="text-muted">แก้ไขข้อมูลของแผนก {{ $department->name }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger animate-shake">
            <strong><i class="bi bi-exclamation-triangle me-2"></i> เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลที่กรอก
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Form Column -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ข้อมูลแผนก</h5>
                    <span class="badge bg-primary">แก้ไขรายละเอียด</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-section">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label">ชื่อแผนก <span class="text-danger">*</span></label>
                                        <div class="input-icon-group">
                                            <i class="bi bi-diagram-3"></i>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                id="name" name="name" value="{{ old('name', $department->name) }}" 
                                                placeholder="ระบุชื่อแผนก" required>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">ชื่อแผนกควรสั้นกระชับ และไม่ซ้ำกับแผนกอื่น</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">รายละเอียด</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                id="description" name="description" rows="4" 
                                                placeholder="รายละเอียดเพิ่มเติมของแผนก (ถ้ามี)">{{ old('description', $department->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> กลับไปหน้ารายการ
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Column -->
        <div class="col-md-4">
            <!-- Department Stats -->
            <div class="card stat-card bg-info text-white mb-4 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนพนักงานในแผนก</h5>
                            <h2 class="display-5 mt-2 mb-0">{{ $departmentUserCount ?? \App\Models\User::where('department_id', $department->id)->count() }}</h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Department Users -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">พนักงานในแผนก</h5>
                    @if(isset($departmentUsers) && count($departmentUsers) > 0)
                        <span class="badge bg-primary">{{ count($departmentUsers) }} คน</span>
                    @else
                        <span class="badge bg-secondary">ไม่มีพนักงาน</span>
                    @endif
                </div>
                
                <div class="card-body p-0">
                    @if(isset($departmentUsers) && count($departmentUsers) > 0)
                        <div class="department-users-list">
                            @foreach($departmentUsers as $user)
                                <div class="user-item">
                                    <div class="user-avatar">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                    <div>
                                        <span class="user-role-badge {{ $user->role == 'admin' ? 'admin' : ($user->role == 'manager' ? 'manager' : '') }}">
                                            {{ $user->role }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                            <p class="mt-3 text-muted">ยังไม่มีพนักงานในแผนกนี้</p>
                            <p class="text-muted small">พนักงานที่ถูกกำหนดให้อยู่ในแผนกนี้จะแสดงที่นี่</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alert after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // ทำให้ textarea ปรับขนาดตามเนื้อหา
        const textarea = document.getElementById('description');
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    });
</script>
@endpush