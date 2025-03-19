@extends('layouts.app')

@section('title', 'เพิ่มสถานะการจ่ายเงินใหม่')

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
            <h1 class="mb-1">เพิ่มสถานะการจ่ายเงินใหม่</h1>
            <p class="text-muted">กรอกข้อมูลเพื่อเพิ่มสถานะการจ่ายเงินใหม่เข้าสู่ระบบ</p>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ข้อมูลสถานะการจ่ายเงิน</h5>
                    <span class="badge bg-primary">เพิ่มข้อมูลใหม่</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.payment_statuses.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">ชื่อสถานะ <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-tag"></i>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name') }}" 
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
                                    id="code" name="code" value="{{ old('code') }}" 
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
                                placeholder="คำอธิบายเพิ่มเติม (ถ้ามี)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="color" class="form-label">สี</label>
                            <div class="color-picker-wrapper">
                                <input type="color" class="form-control form-control-color" 
                                    id="color" name="color" value="{{ old('color', '#6c757d') }}" 
                                    title="เลือกสีที่ต้องการ">
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                    id="color_text" name="color_text" value="{{ old('color_text', '#6c757d') }}" 
                                    placeholder="#RRGGBB" maxlength="20">
                                <div class="color-picker-preview" id="color_preview" style="background-color: {{ old('color', '#6c757d') }};"></div>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
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
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">คำแนะนำ</h5>
                </div>
                <div class="card-body">
                    <div class="form-guide">
                        <h6><i class="bi bi-info-circle me-1"></i> การตั้งชื่อสถานะการจ่ายเงิน</h6>
                        <ul>
                            <li>ควรตั้งชื่อให้สั้น กระชับ และเข้าใจง่าย</li>
                            <li>ตัวอย่างชื่อสถานะที่แนะนำ:</li>
                            <ul>
                                <li>จ่ายเงินสด (Cash)</li>
                                <li>โอนเงิน (Transfer)</li>
                                <li>จ่ายเช็ค (Cheque)</li>
                                <li>บัตรเครดิต (Credit Card)</li>
                            </ul>
                        </ul>
                        
                        <h6 class="mt-3"><i class="bi bi-palette me-1"></i> ตัวอย่างสีที่แนะนำ</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="color-sample" style="background-color: #28a745;" title="#28a745 - สีเขียว (จ่ายเงินสด)" data-color="#28a745"></div>
                            <div class="color-sample" style="background-color: #17a2b8;" title="#17a2b8 - สีฟ้า (โอนเงิน)" data-color="#17a2b8"></div>
                            <div class="color-sample" style="background-color: #ffc107;" title="#ffc107 - สีเหลือง (เช็ค)" data-color="#ffc107"></div>
                            <div class="color-sample" style="background-color: #6c757d;" title="#6c757d - สีเทา (รอดำเนินการ)" data-color="#6c757d"></div>
                            <div class="color-sample" style="background-color: #dc3545;" title="#dc3545 - สีแดง (มีปัญหา)" data-color="#dc3545"></div>
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
        
        // คลิกที่ตัวอย่างสี
        document.querySelectorAll('.color-sample').forEach(sample => {
            sample.addEventListener('click', function() {
                const color = this.getAttribute('data-color');
                colorInput.value = color;
                colorTextInput.value = color;
                colorPreview.style.backgroundColor = color;
            });
        });
    });
</script>
@endpush