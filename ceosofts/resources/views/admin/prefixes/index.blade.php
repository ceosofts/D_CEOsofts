@extends('layouts.app')

@section('title', 'จัดการคำนำหน้าชื่อ')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<link href="{{ asset('css/modal-fix.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="mb-1">จัดการคำนำหน้าชื่อ</h1>
            <p class="text-muted">บริหารจัดการคำนำหน้าชื่อในระบบ</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.prefixes.create') }}" class="btn btn-success add-department-btn">
                <i class="bi bi-plus-circle-fill"></i> เพิ่มคำนำหน้าชื่อใหม่
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

    <!-- Prefixes Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-primary text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนคำนำหน้าชื่อทั้งหมด</h5>
                            <h2 class="display-6">{{ count($prefixes) }}</h2>
                        </div>
                        <i class="bi bi-person-vcard icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-success text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">คำนำหน้าชื่อที่ใช้งานอยู่</h5>
                            <h2 class="display-6">
                                @php
                                    $activeCount = 0;
                                    if (is_object($prefixes) && method_exists($prefixes, 'where')) {
                                        $activeCount = $prefixes->where('is_active', true)->count();
                                    } else {
                                        $activeCount = collect($prefixes)->where('is_active', true)->count();
                                    }
                                @endphp
                                {{ $activeCount }}
                            </h2>
                            <p class="mb-0 small">คำนำหน้าชื่อที่เปิดใช้งาน</p>
                        </div>
                        <i class="bi bi-check-circle icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-info text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนพนักงาน</h5>
                            <h2 class="display-6">{{ $employeeCount ?? '0' }}</h2>
                            <p class="mb-0 small">จำนวนพนักงานในระบบ</p>
                        </div>
                        <i class="bi bi-people icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายการคำนำหน้าชื่อทั้งหมด</h5>
            <span class="badge bg-primary rounded-pill">{{ count($prefixes) }} รายการ</span>
        </div>
        
        <div class="card-body">
            <!-- Search Box -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาคำนำหน้าชื่อ...">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                @if(count($prefixes) > 0)
                    <table class="table table-hover align-middle">
                        <thead class="table-header">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 20%;">คำนำหน้าชื่อ (ไทย)</th>
                                <th style="width: 20%;">คำนำหน้าชื่อ (อังกฤษ)</th>
                                <th style="width: 35%;">คำอธิบาย</th>
                                <th style="width: 10%;" class="text-center">สถานะ</th>
                                <th style="width: 10%;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prefixes as $key => $prefix)
                                <tr class="prefix-row">
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="prefix-name">{{ $prefix->prefix_th }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-date text-secondary"></i> 
                                            สร้างเมื่อ: {{ $prefix->created_at ? $prefix->created_at->format('d/m/Y') : 'ไม่ระบุ' }}
                                        </div>
                                    </td>
                                    <td>{{ $prefix->prefix_en ?? '-' }}</td>
                                    <td>{{ $prefix->description ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($prefix->is_active)
                                            <span class="badge bg-success">ใช้งาน</span>
                                        @else
                                            <span class="badge bg-danger">ไม่ใช้งาน</span>
                                        @endif
                                    </td>
                                    <td class="text-center action-buttons">
                                        <a href="{{ route('admin.prefixes.edit', $prefix->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                data-bs-toggle="modal" data-bs-target="#deletePrefixModal{{ $prefix->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade delete-modal" id="deletePrefixModal{{ $prefix->id }}" tabindex="-1" aria-labelledby="deletePrefixModalLabel{{ $prefix->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deletePrefixModalLabel{{ $prefix->id }}">ยืนยันการลบข้อมูล</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>คุณต้องการลบคำนำหน้าชื่อ <strong>{{ $prefix->prefix_th }}</strong> ใช่หรือไม่?</p>
                                                        
                                                        @if(isset($prefix->employees_count) && $prefix->employees_count > 0)
                                                            <div class="alert alert-warning">
                                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                                <strong>คำเตือน:</strong> คำนำหน้าชื่อนี้มีการใช้งานกับพนักงาน {{ $prefix->employees_count }} รายการ หากลบจะส่งผลต่อข้อมูลพนักงาน
                                                            </div>
                                                        @endif
                                                        
                                                        <p class="text-danger mt-3"><small>การดำเนินการนี้ไม่สามารถย้อนกลับได้</small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <form action="{{ route('admin.prefixes.destroy', ['id' => $prefix->id]) }}" method="POST" class="d-inline">
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
                    
                    <!-- แสดงจำนวนรายการทั้งหมด -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            แสดงรายการทั้งหมด <span id="displayedCount">{{ count($prefixes) }}</span> รายการ
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-person-vcard"></i>
                        </div>
                        <h4>ยังไม่มีข้อมูลคำนำหน้าชื่อ</h4>
                        <p class="text-muted">คลิกปุ่ม "เพิ่มคำนำหน้าชื่อใหม่" เพื่อเริ่มต้นสร้างคำนำหน้าชื่อในระบบ</p>
                        <a href="{{ route('admin.prefixes.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i> เพิ่มคำนำหน้าชื่อใหม่
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
        const tableRows = document.querySelectorAll('tbody tr.prefix-row');
        const displayedCount = document.getElementById('displayedCount');
        
        // Search function
        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const prefixTh = row.querySelector('.prefix-name').textContent.toLowerCase();
                const prefixEn = row.cells[2].textContent.toLowerCase();
                const description = row.cells[3].textContent.toLowerCase();
                
                const matchesSearch = prefixTh.includes(searchText) || 
                                     prefixEn.includes(searchText) || 
                                     description.includes(searchText);
                
                if (matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update displayed count
            if(displayedCount) {
                displayedCount.textContent = visibleCount;
            }
        }
        
        // Add event listener for search input
        if (searchInput) {
            searchInput.addEventListener('keyup', filterTable);
        }
        
        // Auto-hide alert after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // แก้ไขปัญหากับ Modal
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                // ต้องแน่ใจว่าได้เอา backdrop ที่อาจมีอยู่เดิมออกไป
                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                existingBackdrops.forEach(backdrop => {
                    backdrop.remove();
                });
                
                const targetId = this.getAttribute('data-bs-target').replace('#', '');
                const modal = document.getElementById(targetId);
                
                // ทำให้แน่ใจว่า modal จะแสดงข้อมูลอย่างถูกต้อง
                setTimeout(() => {
                    const confirmBtn = modal.querySelector('.delete-confirm-btn');
                    if (confirmBtn) {
                        confirmBtn.style.zIndex = "1060";
                    }
                }, 100);
            });
        });
    });
</script>
@endpush