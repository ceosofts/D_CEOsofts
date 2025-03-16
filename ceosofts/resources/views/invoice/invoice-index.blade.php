@extends('layouts.app')

@section('title', 'รายการใบแจ้งหนี้')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-receipt-cutoff me-2 text-primary"></i> รายการใบแจ้งหนี้</h1>
        <div class="d-flex gap-3">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="ค้นหาใบแจ้งหนี้...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            @can('create invoice')
                <a href="{{ route('invoices.create') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="bi bi-plus-circle me-1"></i> สร้างใบแจ้งหนี้ใหม่
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="mb-3">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary active" data-status="all">
                        <i class="bi bi-list me-1"></i> ทั้งหมด
                    </button>
                    <button type="button" class="btn btn-outline-warning" data-status="pending">
                        <i class="bi bi-hourglass-split me-1"></i> รอชำระเงิน
                    </button>
                    <button type="button" class="btn btn-outline-success" data-status="paid">
                        <i class="bi bi-check-circle me-1"></i> ชำระแล้ว
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-status="overdue">
                        <i class="bi bi-exclamation-triangle me-1"></i> เกินกำหนด
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">เลขที่ใบแจ้งหนี้</th>
                            <th class="py-3">วันที่</th>
                            <th class="py-3">เลขที่ใบเสนอราคา</th>
                            <th class="py-3">ลูกค้า</th>
                            <th class="text-end py-3">จำนวนเงิน</th>
                            <th class="text-end py-3">% การชำระ</th>
                            <th class="text-end py-3">ยอดชำระ</th>
                            <th class="text-end py-3">คงเหลือ</th>
                            <th class="py-3">สถานะ</th>
                            <th class="text-center py-3">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr class="align-middle">
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                <td>{{ $invoice->quotation->quotation_number }}</td>
                                <td>{{ $invoice->quotation->customer_company }}</td>
                                <td class="text-end">฿{{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($invoice->payment_percentage, 2) }}%</td>
                                <td class="text-end">฿{{ number_format($invoice->payment_amount, 2) }}</td>
                                <td class="text-end">฿{{ number_format($invoice->remaining_balance, 2) }}</td>
                                <td>
                                    @if($invoice->is_paid)
                                        <span class="badge rounded-pill bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> ชำระแล้ว
                                        </span>
                                    @elseif($invoice->is_overdue)
                                        <span class="badge rounded-pill bg-danger">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i> เกินกำหนด
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-warning">
                                            <i class="bi bi-clock-fill me-1"></i> รอชำระเงิน
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info text-white" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('edit invoice')
                                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="แก้ไข">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('delete invoice')
                                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="ลบ"
                                                       onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบใบแจ้งหนี้นี้?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="ดาวน์โหลด PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                                    ไม่พบข้อมูลใบแจ้งหนี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-end">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-group .btn {
        margin: 0 1px;
    }
    .badge {
        padding: 0.55em 0.8em;
        font-weight: 500;
    }
    .table > thead {
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .table > :not(:first-child) {
        border-top: none;
    }
    .table th {
        font-weight: 600;
        white-space: nowrap;
    }
    .btn-sm {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }
    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.08);
    }
    #searchInput {
        min-width: 280px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Status filter
    const filterButtons = document.querySelectorAll('[data-status]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const status = this.dataset.status;
            const rows = document.querySelectorAll('tbody tr');
            
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                    return;
                }
                
                const statusCell = row.querySelector('td:nth-child(9)');
                const statusText = statusCell.textContent.toLowerCase();
                row.style.display = statusText.includes(status) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush