@extends('layouts.app')

@section('title', 'จัดการตำแหน่ง')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="mb-1">จัดการตำแหน่ง</h1>
            <p class="text-muted">บริหารจัดการตำแหน่งต่างๆ ภายในองค์กร</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.positions.create') }}" class="btn btn-success add-department-btn">
                <i class="bi bi-plus-circle-fill"></i> เพิ่มตำแหน่งใหม่
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Position Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-primary text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนตำแหน่งทั้งหมด</h5>
                            <h2 class="display-6">{{ count($positions) }}</h2>
                        </div>
                        <i class="bi bi-briefcase icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-success text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนแผนกทั้งหมด</h5>
                            <h2 class="display-6">{{ \App\Models\Department::count() }}</h2>
                        </div>
                        <i class="bi bi-diagram-3 icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-info text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนพนักงานทั้งหมด</h5>
                            <h2 class="display-6">{{ \App\Models\User::count() }}</h2>
                        </div>
                        <i class="bi bi-people icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายการตำแหน่งทั้งหมด</h5>
            <span class="department-count">{{ count($positions) }} รายการ</span>
        </div>
        
        <div class="card-body">
            <!-- Search Box -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาตำแหน่ง...">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                @if(count($positions) > 0)
                    <table class="table table-hover align-middle">
                        <thead class="table-header">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 60%;">ชื่อตำแหน่ง</th>
                                <th style="width: 20%;" class="text-center">จำนวนพนักงาน</th>
                                <th style="width: 15%;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positions as $key => $position)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="department-name">{{ $position->name }}</div>
                                        <div class="department-meta">
                                            <span><i class="bi bi-calendar"></i> สร้างเมื่อ: {{ $position->created_at ? $position->created_at->format('d/m/Y') : 'ไม่ระบุ' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            {{ $position->users_count ?? \App\Models\User::where('position_id', $position->id)->count() }}
                                        </span>
                                    </td>
                                    <td class="text-center action-buttons">
                                        <a href="{{ route('admin.positions.edit', $position->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" data-bs-target="#deletePositionModal{{ $position->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deletePositionModal{{ $position->id }}" tabindex="-1" aria-labelledby="deletePositionModalLabel{{ $position->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deletePositionModalLabel{{ $position->id }}">ยืนยันการลบข้อมูล</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>คุณต้องการลบตำแหน่ง <strong>{{ $position->name }}</strong> ใช่หรือไม่?</p>
                                                        
                                                        @if($position->users_count ?? \App\Models\User::where('position_id', $position->id)->count() > 0)
                                                            <div class="alert alert-warning">
                                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                                <strong>คำเตือน:</strong> ตำแหน่งนี้มีพนักงานอยู่ {{ $position->users_count ?? \App\Models\User::where('position_id', $position->id)->count() }} คน หากลบตำแหน่งนี้ จะส่งผลต่อข้อมูลพนักงานดังกล่าว
                                                            </div>
                                                        @endif
                                                        
                                                        <p class="text-danger mt-3"><small>การดำเนินการนี้ไม่สามารถย้อนกลับได้</small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
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
                    
                    <!-- แสดงจำนวนรายการทั้งหมด -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            แสดงรายการทั้งหมด {{ count($positions) }} รายการ
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h4>ยังไม่มีข้อมูลตำแหน่ง</h4>
                        <p class="text-muted">คลิกปุ่ม "เพิ่มตำแหน่งใหม่" เพื่อเริ่มต้นสร้างตำแหน่งในระบบ</p>
                        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i> เพิ่มตำแหน่งใหม่
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search function
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');
                
                tableRows.forEach(row => {
                    const positionName = row.querySelector('.department-name').textContent.toLowerCase();
                    if (positionName.includes(searchText)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        
        // Auto-hide alert after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endpush