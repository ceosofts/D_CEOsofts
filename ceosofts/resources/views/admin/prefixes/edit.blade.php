@extends('layouts.app')

@section('title', 'แก้ไขคำนำหน้าชื่อ')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@php
use Illuminate\Support\Facades\Schema;
@endphp

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">แก้ไขคำนำหน้าชื่อ</h1>
            <p class="text-muted">แก้ไขข้อมูลของคำนำหน้าชื่อ {{ $prefix->prefix_th }}</p>
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
                    <h5 class="mb-0">ข้อมูลคำนำหน้าชื่อ</h5>
                    <span class="badge bg-primary">แก้ไขข้อมูล</span>
                </div>
                
                <div class="card-body p-4">
                    <!-- แก้ไขการส่ง parameter ให้ route เป็น $prefix->id ให้ชัดเจน -->
                    <form action="{{ route('admin.prefixes.update', ['id' => $prefix->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- ตรวจสอบและแสดงค่า ID สำหรับการดีบัก -->
                        <input type="hidden" name="id" value="{{ $prefix->id }}">
                        
                        <div class="form-group mb-3">
                            <label for="prefix_th" class="form-label">คำนำหน้าชื่อ (ไทย) <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-person-vcard"></i>
                                <input type="text" class="form-control @error('prefix_th') is-invalid @enderror" 
                                    id="prefix_th" name="prefix_th" value="{{ old('prefix_th', $prefix->prefix_th ?? $prefix->name) }}" 
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
                                    id="prefix_en" name="prefix_en" value="{{ old('prefix_en', $prefix->prefix_en) }}" 
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
                                placeholder="คำอธิบายเพิ่มเติม (ถ้ามี)">{{ old('description', $prefix->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $prefix->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('admin.prefixes.index') }}" class="btn btn-secondary">
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
                    <p><i class="bi bi-info-circle me-1"></i> ข้อมูลการใช้งานคำนำหน้าชื่อนี้</p>
                    
                    @if(Schema::hasTable('employees') && Schema::hasColumn('employees', 'prefix_id'))
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                พนักงานที่ใช้คำนำหน้านี้
                                <span class="badge bg-primary rounded-pill">{{ $prefix->employees_count ?? 0 }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-info mb-0">
                            <small><i class="bi bi-info-circle"></i> ไม่พบข้อมูลการเชื่อมโยงกับพนักงาน</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection