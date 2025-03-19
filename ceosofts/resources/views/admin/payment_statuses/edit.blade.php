@extends('layouts.app')

@section('title', 'แก้ไขสถานะการจ่ายเงิน')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<style>
    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .color-picker-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .input-icon-group {
        position: relative;
    }
    .input-icon-group i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .input-icon-group input {
        padding-left: 35px;
    }
    .color-sample {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        display: inline-block;
        margin-right: 5px;
        border: 1px solid #dee2e6;
        cursor: pointer;
    }
</style>
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
            <h1 class="mb-1">แก้ไขสถานะการจ่ายเงิน</h1>
            <p class="text-muted">แก้ไขข้อมูลของสถานะ {{ $payment_status->name }}</p>
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

    @if(session('flash_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('flash_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ข้อมูลสถานะการจ่ายเงิน</h5>
                    <span class="badge bg-primary">แก้ไขข้อมูล</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.payment_statuses.update', $payment_status->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">ชื่อสถานะ <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-tag"></i>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $payment_status->name) }}" 
                                    placeholder="ชื่อสถานะ เช่น จ่ายเงินสด, โอน" required>
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
                                    id="code" name="code" value="{{ old('code', $payment_status->code ?? '') }}" 
                                    placeholder="เช่น CASH, TRANSFER" maxlength="20">
                            </div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">รหัสสั้นๆ สำหรับอ้างอิงในระบบ</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3" 
                                placeholder="คำอธิบายเพิ่มเติม (ถ้ามี)">{{ old('description', $payment_status->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="color" class="form-label">สี</label>
                            <div class="color-picker-wrapper">
                                <input type="color" class="form-control form-control-color" 
                                    id="color" name="color" value="{{ old('color', $payment_status->color ?? '#6c757d') }}" 
                                    title="เลือกสีที่ต้องการ">
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                    id="color_text" name="color_text" value="{{ old('color_text', $payment_status->color ?? '#6c757d') }}" 
                                    placeholder="#RRGGBB" maxlength="20">
                                <div class="color-picker-preview" id="color_preview" style="background-color: {{ old('color', $payment_status->color ?? '#6c757d') }};"></div>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ (old('is_active', $payment_status->is_active ?? true)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                            </div>
                        </div>
                        
                        <div class="form-actions mt-4">
                            <a href="{{ route('admin.payment_statuses.index') }}" class="btn btn-secondary">
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
                            รายการที่ใช้สถานะนี้
                            <span class="badge bg-primary rounded-pill">0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ธุรกรรมทั้งหมด
                            <span class="badge bg-info rounded-pill">0</span>
                        </li>
                    </ul>
                    
                    <div class="mt-3">
                        <h6>ตัวอย่างการแสดงผล</h6>
                        <div class="d-flex align-items-center">
                            @if(isset($payment_status->color) && $payment_status->color)
                                <span class="badge" style="background-color: {{ $payment_status->color }}; color: {{ isDarkColor($payment_status->color) ? 'white' : 'black' }};">
                                    {{ $payment_status->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ $payment_status->name }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <form action="{{ route('admin.payment_statuses.destroy', $payment_status->id) }}" method="POST" 
                              onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสถานะนี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash"></i> ลบข้อมูลสถานะนี้
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection