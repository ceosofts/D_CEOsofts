@extends('layouts.app')

@section('title', 'แก้ไขสถานะสินค้า')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<link href="{{ asset('css/modal-fix.css') }}" rel="stylesheet">
@endpush

@php
// สร้างฟังก์ชัน isDarkColor ให้กับ PHP
function isDarkColor($hexColor) {
    // ถ้าไม่มีค่าหรือไม่ถูกต้อง ให้ถือว่าเป็นสีเข้ม
    if (!$hexColor || !preg_match('/^#[0-9A-F]{6}$/i', $hexColor)) {
        return true;
    }
    
    // แปลงสี HEX เป็นค่า RGB
    $r = hexdec(substr($hexColor, 1, 2));
    $g = hexdec(substr($hexColor, 3, 2));
    $b = hexdec(substr($hexColor, 5, 2));
    
    // คำนวณความสว่าง (ใช้สูตร YIQ)
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    
    // ถ้า YIQ < 128 ถือว่าเป็นสีเข้ม
    return $yiq < 128;
}
@endphp

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">แก้ไขสถานะสินค้า</h1>
            <p class="text-muted">แก้ไขข้อมูลของสถานะ {{ $itemStatus->name }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger animate-shake">
            <strong><i class="bi bi-exclamation-triangle me-2"></i> เกิดข้อผิดพลาด!</strong> กรุณาตรวจสอบข้อมูลและลองอีกครั้ง
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
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ข้อมูลสถานะสินค้า</h5>
                    <span class="badge bg-primary">แก้ไขข้อมูล</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.item_statuses.update', $itemStatus->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">ชื่อสถานะ <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-tag"></i>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $itemStatus->name) }}" 
                                    placeholder="ชื่อสถานะ เช่น พร้อมจำหน่าย, สินค้าหมด" required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">รหัสสถานะ</label>
                            <div class="input-icon-group">
                                <i class="bi bi-code"></i>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                    id="code" name="code" value="{{ old('code', $itemStatus->code) }}" 
                                    placeholder="เช่น AVAIL, OUT" maxlength="20">
                            </div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3" 
                                placeholder="คำอธิบายเพิ่มเติม (ถ้ามี)">{{ old('description', $itemStatus->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="color" class="form-label">สี</label>
                            <div class="color-picker-wrapper">
                                <input type="color" class="form-control form-control-color" 
                                    id="color" name="color" value="{{ old('color', $itemStatus->color) }}" 
                                    title="เลือกสีที่ต้องการ">
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                    id="color_text" name="color_text" value="{{ old('color', $itemStatus->color) }}" 
                                    placeholder="#RRGGBB" maxlength="20">
                                <div class="color-picker-preview" id="color_preview" style="background-color: {{ $itemStatus->color ?? '#6c757d' }};"></div>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $itemStatus->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                            </div>
                        </div>
                        
                        <div class="form-actions mt-4">
                            <a href="{{ route('admin.item_statuses.index') }}" class="btn btn-secondary">
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
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">ข้อมูลการใช้งาน</h5>
                </div>
                <div class="card-body">
                    <p><i class="bi bi-info-circle me-1"></i> ข้อมูลการใช้งานสถานะนี้</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            สินค้าที่ใช้สถานะนี้
                            <span class="badge bg-primary rounded-pill">{{ $itemStatus->products_count ?? 0 }}</span>
                        </li>
                    </ul>
                    
                    <div class="mt-3">
                        <h6>ตัวอย่างการแสดงผล</h6>
                        <div class="d-flex align-items-center">
                            <span class="badge" style="background-color: {{ $itemStatus->color ?? '#6c757d' }}; color: {{ isDarkColor($itemStatus->color ?? '#6c757d') ? 'white' : 'black' }};">
                                {{ $itemStatus->name }}
                            </span>
                        </div>
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
        // สำหรับการจัดการตัวเลือกสี
        const colorInput = document.getElementById('color');
        const colorTextInput = document.getElementById('color_text');
        const colorPreview = document.getElementById('color_preview');
        
        // เมื่อเลือกสีจาก color picker
        colorInput.addEventListener('input', function() {
            colorTextInput.value = this.value;
            colorPreview.style.backgroundColor = this.value;
        });
        
        // เมื่อป้อนค่าสีโดยตรง
        colorTextInput.addEventListener('input', function() {
            const value = this.value;
            if (value.match(/^#[0-9A-F]{6}$/i)) {
                colorInput.value = value;
                colorPreview.style.backgroundColor = value;
            }
        });
    });
</script>
@endpush