@extends('layouts.app')

@section('title', 'โปรไฟล์ผู้ใช้')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="row">
                        <div class="col">
                            <h5 class="fw-bold text-dark">
                                <i class="fas fa-user-circle me-2"></i> โปรไฟล์ผู้ใช้
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> กลับสู่แดชบอร์ด
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                <div class="avatar-circle mx-auto mb-3">
                                    <span class="avatar-text">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <h5 class="fw-bold">{{ $user->name }}</h5>
                                <p class="text-muted">
                                    @if($user->role)
                                        <span class="badge bg-primary">{{ $user->role }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3 text-primary">
                                        <i class="fas fa-info-circle me-2"></i> ข้อมูลทั่วไป
                                    </h6>
                                    <hr>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">ชื่อ-นามสกุล:</div>
                                        <div class="col-md-8">{{ $user->name }}</div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">อีเมล:</div>
                                        <div class="col-md-8">{{ $user->email }}</div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">สิทธิ์การใช้งาน:</div>
                                        <div class="col-md-8">
                                            @if($user->role)
                                                <span class="badge bg-primary">{{ $user->role }}</span>
                                            @else
                                                <span class="badge bg-secondary">ผู้ใช้ทั่วไป</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">สมัครเมื่อ:</div>
                                        <div class="col-md-8">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-user-edit me-1"></i> แก้ไขโปรไฟล์
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 120px;
    height: 120px;
    background-color: #3490dc;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.avatar-text {
    font-size: 48px;
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
}
</style>
@endsection