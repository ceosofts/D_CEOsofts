@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลบริษัท')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-1">แก้ไขข้อมูลบริษัท</h1>
            <p class="text-muted">แก้ไขข้อมูลของบริษัท {{ $company->name ?? 'ไม่ระบุชื่อ' }}</p>
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

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ข้อมูลบริษัท</h5>
            <span class="badge bg-primary">แก้ไขข้อมูล</span>
        </div>
        
        <div class="card-body p-4">
            {{-- Debug Info ถูกปิดไว้เพราะไม่จำเป็นแล้ว 
            @if(config('app.debug'))
                <div class="alert alert-info mb-3">
                    <h5>Debug Information</h5>
                    <pre>{{ print_r($company->toArray(), true) }}</pre>
                </div>
            @endif
            --}}

            <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- คอลัมน์ซ้าย -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="company_name" class="form-label">ชื่อบริษัท <span class="text-danger">*</span></label>
                            <div class="input-icon-group">
                                <i class="bi bi-building"></i>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                    id="company_name" name="company_name" value="{{ old('company_name', $company->company_name) }}" 
                                    placeholder="ชื่อบริษัท" required>
                            </div>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="tax_id" class="form-label">เลขประจำตัวผู้เสียภาษี</label>
                            <div class="input-icon-group">
                                <i class="bi bi-card-text"></i>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" 
                                    id="tax_id" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" 
                                    placeholder="เลขประจำตัวผู้เสียภาษี">
                            </div>
                            @error('tax_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="address" class="form-label">ที่อยู่</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                id="address" name="address" rows="3" 
                                placeholder="ที่อยู่บริษัท">{{ old('address', $company->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="branch_description" class="form-label">สาขา</label>
                            <div class="input-icon-group">
                                <i class="bi bi-building-add"></i>
                                <input type="text" class="form-control @error('branch_description') is-invalid @enderror" 
                                    id="branch_description" name="branch_description" value="{{ old('branch_description', $company->branch_description) }}" 
                                    placeholder="สาขาบริษัท">
                            </div>
                            @error('branch_description')
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
                                    id="phone" name="phone" value="{{ old('phone', $company->phone) }}" 
                                    placeholder="เบอร์โทรศัพท์">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="mobile" class="form-label">เบอร์มือถือ</label>
                            <div class="input-icon-group">
                                <i class="bi bi-phone"></i>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                    id="mobile" name="mobile" value="{{ old('mobile', $company->mobile) }}" 
                                    placeholder="เบอร์มือถือ">
                            </div>
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">อีเมล</label>
                            <div class="input-icon-group">
                                <i class="bi bi-envelope"></i>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email', $company->email) }}" 
                                    placeholder="อีเมล">
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="website" class="form-label">เว็บไซต์</label>
                            <div class="input-icon-group">
                                <i class="bi bi-globe"></i>
                                <input type="text" class="form-control @error('website') is-invalid @enderror" 
                                    id="website" name="website" value="{{ old('website', $company->website) }}" 
                                    placeholder="เว็บไซต์">
                            </div>
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="contact_person" class="form-label">ชื่อผู้ติดต่อ</label>
                            <div class="input-icon-group">
                                <i class="bi bi-person"></i>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                    id="contact_person" name="contact_person" value="{{ old('contact_person', $company->contact_person) }}" 
                                    placeholder="ชื่อผู้ติดต่อ">
                            </div>
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- หัวข้อโซเชียลมีเดีย -->
                <div class="row mt-4 mb-3">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">โซเชียลมีเดีย</h5>
                    </div>
                </div>

                <!-- แถวของโซเชียลมีเดีย -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="facebook" class="form-label">Facebook</label>
                            <div class="input-icon-group">
                                <i class="bi bi-facebook"></i>
                                <input type="text" class="form-control" id="facebook" name="facebook" 
                                    value="{{ old('facebook', $company->facebook) }}" placeholder="URL Facebook">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="twitter" class="form-label">Twitter</label>
                            <div class="input-icon-group">
                                <i class="bi bi-twitter"></i>
                                <input type="text" class="form-control" id="twitter" name="twitter" 
                                    value="{{ old('twitter', $company->twitter) }}" placeholder="URL Twitter">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="instagram" class="form-label">Instagram</label>
                            <div class="input-icon-group">
                                <i class="bi bi-instagram"></i>
                                <input type="text" class="form-control" id="instagram" name="instagram" 
                                    value="{{ old('instagram', $company->instagram) }}" placeholder="URL Instagram">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="linkedin" class="form-label">LinkedIn</label>
                            <div class="input-icon-group">
                                <i class="bi bi-linkedin"></i>
                                <input type="text" class="form-control" id="linkedin" name="linkedin" 
                                    value="{{ old('linkedin', $company->linkedin) }}" placeholder="URL LinkedIn">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="youtube" class="form-label">YouTube</label>
                            <div class="input-icon-group">
                                <i class="bi bi-youtube"></i>
                                <input type="text" class="form-control" id="youtube" name="youtube" 
                                    value="{{ old('youtube', $company->youtube) }}" placeholder="URL YouTube">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="line" class="form-label">LINE</label>
                            <div class="input-icon-group">
                                <i class="bi bi-chat-fill"></i>
                                <input type="text" class="form-control" id="line" name="line" 
                                    value="{{ old('line', $company->line) }}" placeholder="LINE ID">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- โลโก้บริษัท -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="logo" class="form-label">โลโก้บริษัท</label>
                            
                            @if(isset($company->logo) && $company->logo)
                                <div class="mb-2">
                                    <p class="mb-1">โลโก้ปัจจุบัน:</p>
                                    <img src="{{ asset('storage/logos/' . $company->logo) }}" alt="{{ $company->company_name }}" 
                                        style="max-height: 100px; border-radius: 4px;" class="border p-1">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                id="logo" name="logo">
                            <small class="form-text text-muted">อัปโหลดเฉพาะเมื่อต้องการเปลี่ยนโลโก้ (รองรับไฟล์ JPG, PNG ขนาดไม่เกิน 2MB)</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3 mt-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="description" class="form-label">รายละเอียดเพิ่มเติม</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="4" 
                                placeholder="รายละเอียดเพิ่มเติมเกี่ยวกับบริษัท">{{ old('description', $company->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-x-circle me-1"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> บันทึกการแก้ไข
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
                            <p class="mb-1">ตัวอย่างโลโก้ใหม่:</p>
                            <img src="${e.target.result}" alt="Logo preview" style="max-height: 100px; border-radius: 4px;" class="border p-1">
                        `;
                        
                        // ลบตัวอย่างเก่าออก (ถ้ามี)
                        const oldPreview = document.querySelector('.logo-preview');
                        if (oldPreview) {
                            oldPreview.remove();
                        }
                        
                        // แสดงตัวอย่างใหม่
                        preview.classList.add('logo-preview');
                        logoInput.parentNode.appendChild(preview);
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        
        // Auto-resize textarea
        const textarea = document.getElementById('description');
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    });
</script>
@endpush