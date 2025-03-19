@extends('admin.layouts.master')

@section('title', 'จัดการหน่วยนับ')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">จัดการหน่วยนับ</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
        <li class="breadcrumb-item active">หน่วยนับ</li>
    </ol>

    <!-- แสดงข้อความแจ้งเตือน -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error') || isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') ?? $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    รายการหน่วยนับทั้งหมด
                </div>
                <a href="{{ route('admin.units.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> เพิ่มหน่วยนับใหม่
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- ค้นหา -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('admin.units.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                placeholder="ค้นหาหน่วยนับ..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i> ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($units) && $units->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="80">#</th>
                            <th width="120">รหัส</th>
                            <th>ชื่อหน่วยนับ</th>
                            <th>ชื่อภาษาอังกฤษ</th>
                            <th class="text-center" width="100">สถานะ</th>
                            <th class="text-center action-column" width="120">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                        <tr>
                            <td class="text-center">{{ $unit->id }}</td>
                            <td>{{ $unit->unit_code ?? '-' }}</td>
                            <td>{{ $unit->unit_name_th ?? $unit->unit_name ?? $unit->name ?? '-' }}</td>
                            <td>{{ $unit->unit_name_en ?? '-' }}</td>
                            <td class="text-center">
                                @if(isset($unit->is_active))
                                    @if($unit->is_active)
                                    <span class="badge bg-success">ใช้งาน</span>
                                    @else
                                    <span class="badge bg-danger">ไม่ใช้งาน</span>
                                    @endif
                                @elseif(isset($unit->unit_status))
                                    @if($unit->unit_status == 'Active')
                                    <span class="badge bg-success">ใช้งาน</span>
                                    @else
                                    <span class="badge bg-danger">ไม่ใช้งาน</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">ไม่ระบุ</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $unit->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal ยืนยันการลบ -->
                                <div class="modal fade" id="deleteModal{{ $unit->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $unit->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $unit->id }}">ยืนยันการลบ</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                คุณต้องการลบหน่วยนับ <strong>{{ $unit->unit_name_th ?? $unit->unit_name ?? $unit->name ?? 'ID: '.$unit->id }}</strong> ใช่หรือไม่?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger delete-confirm-btn">ยืนยันการลบ</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- แบ่งหน้า -->
            <div class="pagination-container d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    แสดง {{ $units->firstItem() ?? 0 }} ถึง {{ $units->lastItem() ?? 0 }} จากทั้งหมด {{ $units->total() }} รายการ
                </div>
                <div>
                    {{ $units->links() }}
                </div>
            </div>
            @else
            <div class="alert alert-info">
                ไม่พบข้อมูลหน่วยนับ
                @if(request('search'))
                <p class="mb-0">ไม่พบผลลัพธ์สำหรับคำค้นหา: <strong>"{{ request('search') }}"</strong></p>
                <div class="mt-2">
                    <a href="{{ route('admin.units.index') }}" class="btn btn-sm btn-outline-primary">แสดงทั้งหมด</a>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ซ่อนการแจ้งเตือนหลังจาก 5 วินาที
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-info');
        alerts.forEach(function(alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        });
    }, 5000);
</script>
@endpush