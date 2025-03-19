@extends('layouts.app')

@section('title', 'จัดการสถานะการจ่ายเงิน')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<link href="{{ asset('css/modal-fix.css') }}" rel="stylesheet">
<style>
    .color-preview {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    
    .stat-card .icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }
</style>
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="mb-1">จัดการสถานะการจ่ายเงิน</h1>
            <p class="text-muted">บริหารจัดการสถานะการจ่ายเงินในระบบ</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.payment_statuses.create') }}" class="btn btn-success add-department-btn">
                <i class="bi bi-plus-circle-fill"></i> เพิ่มสถานะการจ่ายเงินใหม่
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    @if(session('flash_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('flash_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> เกิดข้อผิดพลาด!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Payment Status Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-primary text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนสถานะทั้งหมด</h5>
                            <h2 class="display-6">{{ $payment_statuses->count() }}</h2>
                        </div>
                        <i class="bi bi-tags icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-success text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">สถานะที่ใช้งานอยู่</h5>
                            <h2 class="display-6">
                                @php
                                    $activeCount = 0;
                                    if (Schema::hasColumn('payment_statuses', 'is_active')) {
                                        $activeCount = $payment_statuses->where('is_active', true)->count();
                                    } else {
                                        $activeCount = $payment_statuses->count();
                                    }
                                @endphp
                                {{ $activeCount }}
                            </h2>
                            <p class="mb-0 small">สถานะที่เปิดใช้งาน</p>
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
                            <h5 class="card-title">จำนวนธุรกรรม</h5>
                            <h2 class="display-6">{{ $transactionCount ?? '0' }}</h2>
                            <p class="mb-0 small">ธุรกรรมในระบบทั้งหมด</p>
                        </div>
                        <i class="bi bi-cash-stack icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายการสถานะการจ่ายเงินทั้งหมด</h5>
            <span class="badge bg-primary rounded-pill">{{ $payment_statuses->count() }} รายการ</span>
        </div>
        
        <div class="card-body">
            <!-- Search & Sort Box -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาสถานะการจ่ายเงิน...">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end align-items-center">
                        <span class="me-2">เรียงโดย: </span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary sort-btn active" data-sort="id">รหัส (ID)</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary sort-btn" data-sort="name">ชื่อสถานะ</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                @if($payment_statuses->count() > 0)
                    <table class="table table-hover align-middle">
                        <thead class="table-header">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 25%;">ชื่อสถานะ</th>
                                <th style="width: 35%;">คำอธิบาย</th>
                                <th style="width: 15%;" class="text-center">สี</th>
                                <th style="width: 10%;" class="text-center">สถานะ</th>
                                <th style="width: 10%;" class="text-center action-column">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment_statuses as $key => $status)
                                <tr class="payment-status-row">
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="status-name">{{ $status->name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-date text-secondary"></i> 
                                            สร้างเมื่อ: {{ $status->created_at ? $status->created_at->format('d/m/Y') : 'ไม่ระบุ' }}
                                        </div>
                                    </td>
                                    <td>{{ $status->description ?? '-' }}</td>
                                    <td class="text-center">
                                        @if(isset($status->color) && $status->color)
                                            <span class="color-preview" style="background-color: {{ $status->color }};"></span>
                                            <span class="small ms-2">{{ $status->color }}</span>
                                        @else
                                            <span class="text-muted">- ไม่ระบุ -</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($status->is_active))
                                            @if($status->is_active)
                                                <span class="badge bg-success">ใช้งาน</span>
                                            @else
                                                <span class="badge bg-danger">ไม่ใช้งาน</span>
                                            @endif
                                        @else
                                            <span class="badge bg-success">ใช้งาน</span>
                                        @endif
                                    </td>
                                    <td class="text-center action-column">
                                        <!-- แก้ไข: ลบข้อความแปลกๆ ออก -->
                                        <a href="{{ route('admin.payment_statuses.edit', $status->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                data-bs-toggle="modal" data-bs-target="#deleteStatusModal{{ $status->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade delete-modal" id="deleteStatusModal{{ $status->id }}" tabindex="-1" aria-labelledby="deleteStatusModalLabel{{ $status->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteStatusModalLabel{{ $status->id }}">ยืนยันการลบข้อมูล</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>คุณต้องการลบสถานะ <strong>{{ $status->name }}</strong> ใช่หรือไม่?</p>
                                                        <p class="text-danger mt-3"><small>การดำเนินการนี้ไม่สามารถย้อนกลับได้</small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <form action="{{ route('admin.payment_statuses.destroy', $status->id) }}" method="POST" class="d-inline">
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
                            แสดงรายการทั้งหมด <span id="displayedCount">{{ $payment_statuses->count() }}</span> รายการ
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h4>ยังไม่มีข้อมูลสถานะการจ่ายเงิน</h4>
                        <p class="text-muted">คลิกปุ่ม "เพิ่มสถานะการจ่ายเงินใหม่" เพื่อเริ่มต้นสร้างสถานะในระบบ</p>
                        <a href="{{ route('admin.payment_statuses.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i> เพิ่มสถานะการจ่ายเงินใหม่
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
        const tableRows = document.querySelectorAll('tbody tr.payment-status-row');
        const displayedCount = document.getElementById('displayedCount');
        
        // Search function
        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const statusName = row.querySelector('.status-name').textContent.toLowerCase();
                let description = '-';
                if (row.cells[2]) {
                    description = row.cells[2].textContent.toLowerCase();
                }
                
                const matchesSearch = statusName.includes(searchText) || 
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

        // Sort function
        const sortButtons = document.querySelectorAll('.sort-btn');
        const tbody = document.querySelector('tbody');
        let currentSort = 'id'; // Default sort by ID
        
        function sortTable(sortBy) {
            const rows = Array.from(tableRows);
            
            rows.sort((a, b) => {
                let aValue, bValue;
                
                if (sortBy === 'id') {
                    // Sort by row index (implicit ID order)
                    aValue = parseInt(a.querySelector('td:first-child').textContent);
                    bValue = parseInt(b.querySelector('td:first-child').textContent);
                } else if (sortBy === 'name') {
                    // Sort by name
                    aValue = a.querySelector('.status-name').textContent.toLowerCase();
                    bValue = b.querySelector('.status-name').textContent.toLowerCase();
                }
                
                if (aValue < bValue) return -1;
                if (aValue > bValue) return 1;
                return 0;
            });
            
            // Re-append rows in sorted order
            rows.forEach(row => tbody.appendChild(row));
            
            // Update active button
            sortButtons.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.sort === sortBy);
            });
            
            currentSort = sortBy;
        }
        
        // Add event listener for sort buttons
        sortButtons.forEach(button => {
            button.addEventListener('click', () => {
                const sortBy = button.dataset.sort;
                sortTable(sortBy);
            });
        });
    });
</script>
@endpush