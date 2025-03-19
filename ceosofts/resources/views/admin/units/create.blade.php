@extends('layouts.app')

@section('title', 'เพิ่มหน่วยวัดใหม่')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">เพิ่มหน่วยวัดใหม่</h1>
            <p class="text-muted">กรอกข้อมูลด้านล่างเพื่อสร้างหน่วยวัดใหม่ในระบบ</p>
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
                    <h5 class="mb-0">ข้อมูลหน่วยวัด</h5>
                    <span class="badge bg-success">กรอกข้อมูลให้ครบถ้วน</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.units.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="unit_name_th" class="form-label">ชื่อหน่วยวัด (ไทย) <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-rulers"></i>
                                <input type="text" class="form-control @error('unit_name_th') is-invalid @enderror" 
                                    id="unit_name_th" name="unit_name_th" value="{{ old('unit_name_th') }}" 
                                    placeholder="ชื่อหน่วยวัดภาษาไทย" required>
                            </div>
                            @error('unit_name_th')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="unit_name_en" class="form-label">ชื่อหน่วยวัด (อังกฤษ)</label>
                            <div class="input-icon-group">
                                <i class="bi bi-rulers"></i>
                                <input type="text" class="form-control @error('unit_name_en') is-invalid @enderror" 
                                    id="unit_name_en" name="unit_name_en" value="{{ old('unit_name_en') }}" 
                                    placeholder="ชื่อหน่วยวัดภาษาอังกฤษ">
                            </div>
                            @error('unit_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3" 
                                placeholder="คำอธิบายเพิ่มเติม">{{ old('description') }}</textarea>
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
                            <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">
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
                <h6><i class="bi bi-info-circle me-1"></i> คำแนะนำในการเพิ่มหน่วยวัด</h6>
                <ul>
                    <li>ชื่อหน่วยวัดภาษาไทยจำเป็นต้องกรอก</li>
                    <li>ชื่อหน่วยวัดภาษาอังกฤษเป็นตัวเลือกเสริม</li>
                    <li>หน่วยวัดที่เพิ่มเข้ามาจะสามารถใช้กับสินค้าได้ทันที</li>
                    <li>ตัวอย่างหน่วยวัด เช่น ชิ้น, อัน, กล่อง, ชุด, เมตร</li>
                </ul>
                
                <div class="card bg-light mt-3">
                    <div class="card-body p-3">
                        <h6 class="card-title"><i class="bi bi-lightbulb me-2"></i>ตัวอย่างหน่วยวัด</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>ชิ้น</td>
                                    <td>Piece</td>
                                </tr>
                                <tr>
                                    <td>อัน</td>
                                    <td>Item</td>
                                </tr>
                                <tr>
                                    <td>กล่อง</td>
                                    <td>Box</td>
                                </tr>
                                <tr>
                                    <td>เมตร</td>
                                    <td>Meter</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection