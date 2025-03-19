@extends('layouts.app')

@section('title', 'เพิ่มตำแหน่งใหม่')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">เพิ่มตำแหน่งใหม่</h1>
            <p class="text-muted">กรอกข้อมูลด้านล่างเพื่อสร้างตำแหน่งใหม่ในระบบ</p>
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
                    <h5 class="mb-0">ข้อมูลตำแหน่ง</h5>
                    <span class="badge bg-success">กรอกข้อมูลให้ครบถ้วน</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.positions.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-section">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label">ชื่อตำแหน่ง <span class="text-danger">*</span></label>
                                        <div class="input-icon-group">
                                            <i class="bi bi-briefcase"></i>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                id="name" name="name" value="{{ old('name') }}" 
                                                placeholder="ระบุชื่อตำแหน่ง" required autofocus>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">ชื่อตำแหน่งควรสั้นกระชับ และไม่ซ้ำกับตำแหน่งอื่น</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">รายละเอียด</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                id="description" name="description" rows="4" 
                                                placeholder="รายละเอียดเพิ่มเติมของตำแหน่ง (ถ้ามี)">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">
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
                <h6><i class="bi bi-info-circle me-1"></i> คำแนะนำในการสร้างตำแหน่ง</h6>
                <ul>
                    <li>ชื่อตำแหน่งควรมีความชัดเจน เข้าใจง่าย</li>
                    <li>ตรวจสอบให้แน่ใจว่าชื่อไม่ซ้ำกับตำแหน่งที่มีอยู่แล้ว</li>
                    <li>ระบุรายละเอียดของตำแหน่งให้ชัดเจนเพื่อใช้ในการอ้างอิง</li>
                </ul>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-briefcase me-2"></i> ตำแหน่งในระบบ</h5>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted mb-3">หลังจากเพิ่มตำแหน่งแล้ว คุณสามารถกำหนดให้พนักงานดำรงตำแหน่งนั้นได้</p>
                    
                    <div class="d-grid">
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> กลับไปยังรายการตำแหน่ง
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