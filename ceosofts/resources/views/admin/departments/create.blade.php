@extends('layouts.app')

@section('title', 'เพิ่มแผนกใหม่')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<style>
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        border-bottom: 2px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    
    .animate-shake {
        animation: shake 0.82s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }
    
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
    
    .form-section {
        padding: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border-color: #dee2e6;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        border-color: #0d6efd;
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .input-icon-group {
        position: relative;
    }
    
    .input-icon-group i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    
    .input-icon-group .form-control {
        padding-left: 2.5rem;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: all 0.3s;
    }
    
    .btn-primary {
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.25);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.35);
    }
    
    .btn-secondary {
        box-shadow: 0 2px 6px rgba(108, 117, 125, 0.15);
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.25);
    }
    
    .form-actions {
        padding-top: 1rem;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }
    
    /* เพิ่มคำแนะนำสำหรับฟอร์ม */
    .form-guide {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 1.25rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }
    
    .form-guide h6 {
        color: #0d6efd;
        margin-bottom: 0.5rem;
    }
    
    .form-guide ul {
        padding-left: 1.25rem;
        margin-bottom: 0;
    }
    
    .form-guide li {
        margin-bottom: 0.5rem;
    }
    
    .text-accent {
        color: #0d6efd;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">เพิ่มแผนกใหม่</h1>
            <p class="text-muted">กรอกข้อมูลด้านล่างเพื่อสร้างแผนกใหม่ในระบบ</p>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ข้อมูลแผนก</h5>
                    <span class="badge bg-success">กรอกข้อมูลให้ครบถ้วน</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.departments.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-section">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label">ชื่อแผนก <span class="text-danger">*</span></label>
                                        <div class="input-icon-group">
                                            <i class="bi bi-diagram-3"></i>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                id="name" name="name" value="{{ old('name') }}" 
                                                placeholder="ระบุชื่อแผนก" required autofocus>
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
                                                placeholder="รายละเอียดเพิ่มเติมของแผนก (ถ้ามี)">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-guide">
                <h6><i class="bi bi-info-circle me-1"></i> คำแนะนำในการสร้างแผนก</h6>
                <ul>
                    <li>ชื่อแผนกควรมีความชัดเจน เข้าใจง่าย</li>
                    <li>ตรวจสอบให้แน่ใจว่าชื่อไม่ซ้ำกับแผนกที่มีอยู่แล้ว</li>
                    <li>ควรใส่รายละเอียดที่จำเป็นเพื่อให้ผู้ใช้เข้าใจหน้าที่ของแผนก</li>
                </ul>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i> แผนกในระบบ</h5>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted mb-3">หลังจากเพิ่มแผนกแล้ว คุณสามารถจัดการพนักงานให้เข้าสู่แผนกได้</p>
                    
                    <div class="d-grid">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> กลับไปยังรายการแผนก
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-resize textarea
        const textarea = document.getElementById('description');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    });
</script>
@endpush