@extends('layouts.app')

@section('title', 'เพิ่มคำนำหน้าชื่อใหม่')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">เพิ่มคำนำหน้าชื่อใหม่</h1>
            <p class="text-muted">กรอกข้อมูลเพื่อเพิ่มคำนำหน้าชื่อใหม่เข้าสู่ระบบ</p>
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
                    <h5 class="mb-0">ข้อมูลคำนำหน้าชื่อ</h5>
                    <span class="badge bg-primary">เพิ่มข้อมูลใหม่</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.prefixes.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="prefix_th" class="form-label">คำนำหน้าชื่อ (ไทย) <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-person-vcard"></i>
                                <input type="text" class="form-control @error('prefix_th') is-invalid @enderror" 
                                    id="prefix_th" name="prefix_th" value="{{ old('prefix_th') }}" 
                                    placeholder="เช่น นาย, นาง, นางสาว" required>
                            </div>
                            @error('prefix_th')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="prefix_en" class="form-label">คำนำหน้าชื่อ (อังกฤษ)</label>
                            <div class="input-icon-group">
                                <i class="bi bi-person-vcard"></i>
                                <input type="text" class="form-control @error('prefix_en') is-invalid @enderror" 
                                    id="prefix_en" name="prefix_en" value="{{ old('prefix_en') }}" 
                                    placeholder="เช่น Mr., Mrs., Miss">
                            </div>
                            @error('prefix_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('admin.prefixes.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> ยกเลิก
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
                        <h6><i class="bi bi-info-circle me-1"></i> การตั้งชื่อคำนำหน้าชื่อ</h6>
                        <ul>
                            <li>คำนำหน้าชื่อภาษาไทย: เช่น นาย, นาง, นางสาว</li>
                            <li>คำนำหน้าชื่อภาษาอังกฤษ: เช่น Mr., Mrs., Miss</li>
                            <li>ควรตั้งชื่อให้สั้น กระชับ และเข้าใจง่าย</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection