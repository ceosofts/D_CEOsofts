@extends('layouts.app')

@section('title', 'แก้ไขหน่วยวัด')

@push('styles')
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">แก้ไขหน่วยวัด</h1>
            <p class="text-muted">แก้ไขข้อมูลของหน่วยวัด {{ $unit->unit_name }}</p>
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
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ข้อมูลหน่วยวัด</h5>
                    <span class="badge bg-primary">แก้ไขข้อมูล</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.units.update', $unit->unit_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="unit_code" class="form-label">รหัสหน่วยวัด <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-upc"></i>
                                <input type="text" class="form-control @error('unit_code') is-invalid @enderror" 
                                    id="unit_code" name="unit_code" value="{{ old('unit_code', $unit->unit_code) }}" 
                                    placeholder="รหัสหน่วยวัด" required>
                            </div>
                            @error('unit_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="unit_name" class="form-label">ชื่อหน่วยวัด <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-rulers"></i>
                                <input type="text" class="form-control @error('unit_name') is-invalid @enderror" 
                                    id="unit_name" name="unit_name" value="{{ old('unit_name', $unit->unit_name) }}" 
                                    placeholder="ชื่อหน่วยวัด" required>
                            </div>
                            @error('unit_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="unit_status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select class="form-select @error('unit_status') is-invalid @enderror" 
                                id="unit_status" name="unit_status" required>
                                <option value="Active" {{ (old('unit_status', $unit->unit_status) == 'Active') ? 'selected' : '' }}>ใช้งาน</option>
                                <option value="Inactive" {{ (old('unit_status', $unit->unit_status) == 'Inactive') ? 'selected' : '' }}>ไม่ใช้งาน</option>
                            </select>
                            @error('unit_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">
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
                    <p><i class="bi bi-info-circle me-1"></i> ข้อมูลการใช้งานหน่วยวัดนี้</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            สินค้าที่ใช้หน่วยวัดนี้
                            <span class="badge bg-primary rounded-pill">{{ isset($productCount) ? $productCount : 0 }}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-light text-muted">
                    <small>
                        <i class="bi bi-clock-history me-1"></i> แก้ไขล่าสุด: {{ $unit->updated_at ? $unit->updated_at->format('d/m/Y H:i') : 'ไม่มีข้อมูล' }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // เพิ่มเอฟเฟคการแสดงผลของฟอร์ม
        const card = document.querySelector('.card');
        if (card) {
            card.classList.add('fade-in');
        }
    });
</script>
@endpush