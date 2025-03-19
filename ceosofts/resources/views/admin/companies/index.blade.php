@extends('layouts.app')

@section('title', 'จัดการบริษัท')

@push('styles')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container fade-in">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="mb-1">จัดการข้อมูลบริษัท</h1>
            <p class="text-muted">บริหารจัดการข้อมูลบริษัทในระบบ</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.companies.create') }}" class="btn btn-success add-department-btn">
                <i class="bi bi-building-add"></i> เพิ่มบริษัทใหม่
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

    <!-- Companies Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-primary text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">จำนวนบริษัททั้งหมด</h5>
                            <h2 class="display-6">{{ count($companies) }}</h2>
                        </div>
                        <i class="bi bi-building icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-success text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">ผู้ติดต่อของบริษัท</h5>
                            <h2 class="display-6">{{ $companies->whereNotNull('contact_person')->count() }}</h2>
                            <p class="mb-0 small">บริษัทที่มีรายละเอียดผู้ติดต่อ</p>
                        </div>
                        <i class="bi bi-person-rolodex icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card stat-card bg-info text-white department-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">ผู้ใช้งานที่เชื่อมโยง</h5>
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
            <h5 class="mb-0">รายการบริษัททั้งหมด</h5>
            <span class="department-count">{{ count($companies) }} รายการ</span>
        </div>
        
        <div class="card-body">
            <!-- Search Box -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาบริษัท...">
                    </div>
                </div>
                <!-- นำปุ่มกรองสถานะออกเนื่องจากไม่มีข้อมูลในฐานข้อมูล -->
            </div>
            
            <div class="table-responsive">
                @if(count($companies) > 0)
                    <table class="table table-hover align-middle">
                        <thead class="table-header">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 25%;">ชื่อบริษัท</th>
                                <th style="width: 15%;">สาขา</th> <!-- เปลี่ยนจากสถานะเป็นสาขา -->
                                <th style="width: 20%;">ที่อยู่</th>
                                <th style="width: 15%;">เบอร์โทรศัพท์</th>
                                <th style="width: 10%;">เลขประจำตัวผู้เสียภาษี</th>
                                <th style="width: 10%;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $key => $company)
                                <tr class="company-row">
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($company->logo)
                                                    <img src="{{ asset('storage/logos/' . $company->logo) }}" alt="{{ $company->company_name }}" class="rounded" width="40" height="40">
                                                @else
                                                    <div class="user-avatar">
                                                        {{ substr($company->company_name, 0, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="department-name">{{ $company->company_name }}</div>
                                                <div class="department-meta">
                                                    <span><i class="bi bi-calendar"></i> สร้างเมื่อ: {{ $company->created_at ? $company->created_at->format('d/m/Y') : 'ไม่ระบุ' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $company->branch_description ?? 'สำนักงานใหญ่' }}</td> <!-- แสดงข้อมูลสาขาแทนสถานะ -->
                                    <td>{{ Str::limit($company->address, 50) }}</td>
                                    <td>{{ $company->phone ?? 'ไม่ระบุ' }}</td>
                                    <td>{{ $company->tax_id ?? 'ไม่ระบุ' }}</td>
                                    <td class="text-center action-buttons">
                                        <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" data-bs-target="#deleteCompanyModal{{ $company->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteCompanyModal{{ $company->id }}" tabindex="-1" aria-labelledby="deleteCompanyModalLabel{{ $company->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteCompanyModalLabel{{ $company->id }}">ยืนยันการลบข้อมูล</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>คุณต้องการลบบริษัท <strong>{{ $company->company_name }}</strong> ใช่หรือไม่?</p>
                                                        
                                                        @if(isset($company->users_count) && $company->users_count > 0)
                                                            <div class="alert alert-warning">
                                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                                <strong>คำเตือน:</strong> มีผู้ใช้งาน {{ $company->users_count }} คน ที่เชื่อมโยงกับบริษัทนี้
                                                            </div>
                                                        @endif
                                                        
                                                        <p class="text-danger mt-3"><small>การดำเนินการนี้ไม่สามารถย้อนกลับได้</small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST">
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
                            แสดงรายการทั้งหมด <span id="displayedCount">{{ count($companies) }}</span> รายการ
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h4>ยังไม่มีข้อมูลบริษัท</h4>
                        <p class="text-muted">คลิกปุ่ม "เพิ่มบริษัทใหม่" เพื่อเริ่มต้นสร้างข้อมูลบริษัทในระบบ</p>
                        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i> เพิ่มบริษัทใหม่
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
        // Search function - ปรับปรุงให้เรียบง่ายขึ้น ไม่มีการกรองตามสถานะ
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr.company-row');
        const displayedCount = document.getElementById('displayedCount');
        
        // Search function
        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                // แก้ไขชื่อฟิลด์ให้ตรงกับที่แสดงในตาราง
                const companyName = row.querySelector('.department-name').textContent.toLowerCase();
                const branch = row.cells[2].textContent.toLowerCase();
                const address = row.cells[3].textContent.toLowerCase();
                const phone = row.cells[4].textContent.toLowerCase();
                const taxId = row.cells[5].textContent.toLowerCase();
                
                const matchesSearch = companyName.includes(searchText) || 
                                     branch.includes(searchText) ||
                                     address.includes(searchText) || 
                                     phone.includes(searchText) || 
                                     taxId.includes(searchText);
                
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
    });
</script>
@endpush