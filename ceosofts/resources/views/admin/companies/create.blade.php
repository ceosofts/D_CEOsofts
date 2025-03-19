@extends('layouts.app')

@section('title', 'เพิ่มบริษัทใหม่')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">เพิ่มบริษัทใหม่</h1>
            <p class="text-muted">กรอกข้อมูลด้านล่างเพื่อเพิ่มบริษัทใหม่เข้าสู่ระบบ</p>
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

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ข้อมูลบริษัท</h5>
            <span class="badge bg-success">กรอกข้อมูลให้ครบถ้วน</span>
        </div>
        
        <div class="card-body p-4">
            <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- คอลัมน์ซ้าย -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">ชื่อบริษัท <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-building"></i>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name') }}" 
                                    placeholder="ชื่อบริษัท" required autofocus>
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="address" class="form-label">ที่อยู่</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                id="address" name="address" rows="3" 
                                placeholder="ที่อยู่บริษัท">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="logo" class="form-label">โลโก้บริษัท</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                id="logo" name="logo" accept="image/*">
                            <small class="form-text text-muted">ไฟล์ภาพรูปแบบ JPG, PNG ขนาดไม่เกิน 2MB</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- คอลัมน์ขวา -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                            <div class="input-icon-group">
                                <i class="bi bi-telephone"></i>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                    id="phone" name="phone" value="{{ old('phone') }}" 
                                    placeholder="เบอร์โทรศัพท์">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="tax_id" class="form-label">เลขประจำตัวผู้เสียภาษี</label>
                            <div class="input-icon-group">
                                <i class="bi bi-card-text"></i>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" 
                                    id="tax_id" name="tax_id" value="{{ old('tax_id') }}" 
                                    placeholder="เลขประจำตัวผู้เสียภาษี">
                            </div>
                            @error('tax_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="website" class="form-label">เว็บไซต์</label>
                            <div class="input-icon-group">
                                <i class="bi bi-globe"></i>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                    id="website" name="website" value="{{ old('website') }}" 
                                    placeholder="https://example.com">
                            </div>
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                            <label class="form-check-label" for="active">เปิดใช้งาน</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // แสดงตัวอย่างรูปก่อนอัปโหลด
        const logoInput = document.getElementById('logo');
        if (logoInput) {
            logoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'mt-2';
                        preview.innerHTML = `
                            <p class="mb-1">ตัวอย่างโลโก้:</p>
                            <img src="${e.target.result}" alt="Logo preview" style="max-height: 100px; border-radius: 4px;" class="border p-1">
                        `;
                        
                        // ลบตัวอย่างเก่าออก (ถ้ามี)
                        const oldPreview = logoInput.parentNode.querySelector('.mt-2');
                        if (oldPreview) {
                            oldPreview.remove();
                        }
                        
                        // แสดงตัวอย่างใหม่
                        logoInput.parentNode.appendChild(preview);
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
@endpush