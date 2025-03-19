@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<link href="{{ asset('css/modal-fix.css') }}" rel="stylesheet">
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #495057;
    }
    
    .stat-card .icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }
    
    .user-role-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .user-role-badge.admin {
        background-color: #dc3545;
        color: white;
    }
    
    .user-role-badge.staff {
        background-color: #28a745;
        color: white;
    }
    
    .user-role-badge.manager {
        background-color: #ffc107;
        color: #212529;
    }
</style>
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="mb-1">จัดการผู้ใช้งานระบบ</h1>
            <p class="text-muted">บริหารจัดการบัญชีผู้ใช้งานทั้งหมดในระบบ</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                <i class="bi bi-person-plus-fill"></i> เพิ่มผู้ใช้งานใหม่
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

    <!-- Users Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนผู้ใช้ทั้งหมด</h5>
                            <h2 class="display-6">{{ count($users) }}</h2>
                        </div>
                        <i class="bi bi-people icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">พนักงาน (Staff)</h5>
                            <h2 class="display-6">{{ $users->where('role', 'staff')->count() }}</h2>
                        </div>
                        <i class="bi bi-person-badge icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">ผู้ดูแลระบบ</h5>
                            <h2 class="display-6">{{ $users->where('role', 'admin')->count() }}</h2>
                        </div>
                        <i class="bi bi-shield-lock icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">ผู้จัดการ</h5>
                            <h2 class="display-6">{{ $users->where('role', 'manager')->count() }}</h2>
                        </div>
                        <i class="bi bi-person-workspace icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายชื่อผู้ใช้งานทั้งหมด</h5>
            <span class="badge bg-primary rounded-pill">{{ count($users) }} รายการ</span>
        </div>
        
        <div class="card-body">
            <!-- Search and Filter Box -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="search-box">
                        {{-- <i class="bi bi-search search-icon"></i> --}}
                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาผู้ใช้งาน (ชื่อ, อีเมล หรือแผนก)...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="roleFilter">
                        <option value="">ทุกประเภทผู้ใช้</option>
                        <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                        <option value="manager">ผู้จัดการ (Manager)</option>
                        <option value="staff">พนักงาน (Staff)</option>
                    </select>
                </div>
            </div>
            
            <div class="table-responsive">
                @if(count($users) > 0)
                    <table class="table table-hover align-middle">
                        <thead class="table-header">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 30%;">ชื่อ-นามสกุล</th>
                                <th style="width: 20%;">อีเมล</th>
                                <th style="width: 15%;">แผนก</th>
                                <th style="width: 15%;" class="text-center">ประเภทผู้ใช้</th>
                                <th style="width: 15%;" class="text-center action-column">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $key => $user)
                                <tr class="user-row" data-role="{{ $user->role }}">
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="department-name">{{ $user->name }}</div>
                                                <div class="text-muted small">
                                                    <i class="bi bi-calendar text-secondary"></i> 
                                                    สร้างเมื่อ: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'ไม่ระบุ' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->department ? $user->department->name : 'ไม่ระบุ' }}</td>
                                    <td class="text-center">
                                        <span class="user-role-badge {{ $user->role }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="text-center action-column">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">ยืนยันการลบข้อมูล</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>คุณต้องการลบผู้ใช้ <strong>{{ $user->name }}</strong> ใช่หรือไม่?</p>
                                                        
                                                        @if($user->id == Auth::id())
                                                            <div class="alert alert-danger">
                                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                                <strong>ไม่สามารถดำเนินการได้:</strong> คุณไม่สามารถลบบัญชีของตัวเองได้
                                                            </div>
                                                        @endif
                                                        
                                                        <p class="text-danger mt-3"><small>การดำเนินการนี้ไม่สามารถย้อนกลับได้</small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        @if($user->id != Auth::id())
                                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger delete-confirm-btn">ยืนยันการลบ</button>
                                                            </form>
                                                        @endif
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
                            แสดงรายการทั้งหมด <span id="displayedCount">{{ count($users) }}</span> รายการ
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4>ยังไม่มีข้อมูลผู้ใช้งาน</h4>
                        <p class="text-muted">คลิกปุ่ม "เพิ่มผู้ใช้งานใหม่" เพื่อเริ่มต้นสร้างบัญชีผู้ใช้ในระบบ</p>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-person-plus me-2"></i> เพิ่มผู้ใช้งานใหม่
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
        const roleFilter = document.getElementById('roleFilter');
        const tableRows = document.querySelectorAll('tbody tr.user-row');
        const displayedCount = document.getElementById('displayedCount');
        
        // Combined search and filter function
        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            const selectedRole = roleFilter.value.toLowerCase();
            
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const name = row.querySelector('.department-name').textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const department = row.cells[3].textContent.toLowerCase();
                const role = row.dataset.role.toLowerCase();
                
                const matchesSearch = name.includes(searchText) || 
                                      email.includes(searchText) || 
                                      department.includes(searchText);
                                      
                const matchesFilter = selectedRole === '' || role === selectedRole;
                
                if (matchesSearch && matchesFilter) {
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
        
        // Add event listeners
        if (searchInput) {
            searchInput.addEventListener('keyup', filterTable);
        }
        
        if (roleFilter) {
            roleFilter.addEventListener('change', filterTable);
        }
        
        // Auto-hide alert after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // Modal fix for delete buttons
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove any existing backdrops
                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                existingBackdrops.forEach(backdrop => {
                    backdrop.remove();
                });
                
                const targetId = this.getAttribute('data-bs-target').replace('#', '');
                const modal = document.getElementById(targetId);
                
                // Ensure the modal shows correctly
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