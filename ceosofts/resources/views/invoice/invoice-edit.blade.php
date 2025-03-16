@extends('layouts.app')

@section('title', 'แก้ไขใบแจ้งหนี้')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil-square me-2 text-primary"></i> แก้ไขใบแจ้งหนี้ #{{ $invoice->invoice_number }}</h1>
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> กลับไปยังรายละเอียด
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> กรุณาตรวจสอบข้อมูลที่กรอก
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm" onsubmit="return validateForm()">
        @csrf
        @method('PUT')
        
        <!-- Hidden fields for calculated values -->
        <input type="hidden" name="actual_payment_amount" id="actual_payment_amount">
        <input type="hidden" name="actual_remaining_balance" id="actual_remaining_balance">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="m-0"><i class="bi bi-receipt me-2"></i> รายละเอียดใบแจ้งหนี้</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">เลขที่ใบแจ้งหนี้</label>
                            <input type="text" class="form-control bg-light non-currency" value="{{ $invoice->invoice_number }}" readonly>
                            <small class="text-muted">เลขที่ใบแจ้งหนี้ถูกสร้างอัตโนมัติ</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">วันที่ใบแจ้งหนี้</label>
                            <input type="date" name="invoice_date" class="form-control" 
                                   value="{{ old('invoice_date', $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y-m-d') : $invoice->invoice_date) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">กำหนดชำระ</label>
                            <input type="date" name="due_date" class="form-control" 
                                   value="{{ old('due_date', $invoice->due_date instanceof \Carbon\Carbon ? $invoice->due_date->format('Y-m-d') : $invoice->due_date) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">สถานะ</label>
                            <select name="status_id" class="form-select" required>
                                <option value="">-- เลือกสถานะ --</option>
                                @foreach($jobStatuses as $status)
                                    <option value="{{ $status->id }}" 
                                            {{ old('status_id', $invoice->status_id) == $status->id ? 'selected' : '' }}
                                            data-color="{{ $status->color ?? '#6c757d' }}">
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">เงื่อนไขการชำระเงิน</label>
                            <input type="text" name="payment_terms" class="form-control"
                                   value="{{ old('payment_terms', $invoice->payment_terms) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">หมายเหตุ</label>
                            <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $invoice->remarks ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="m-0"><i class="bi bi-cash-coin me-2"></i> ข้อมูลการชำระเงิน</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">จำนวนเงินทั้งหมด</label>
                            <div class="input-group">
                                <span class="input-group-text">฿</span>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ number_format($invoice->total_amount, 2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">เปอร์เซ็นต์การชำระ (%)</label>
                            <div class="input-group">
                                <input type="number" name="payment_percentage" class="form-control" 
                                       value="{{ old('payment_percentage', $invoice->payment_percentage) }}"
                                       min="0" max="100" step="0.01" required
                                       onchange="calculatePayment()">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">จำนวนเงินที่ต้องชำระ</label>
                            <div class="input-group">
                                <span class="input-group-text">฿</span>
                                <input type="text" id="payment_amount_display" class="form-control bg-light" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">ยอดคงเหลือ</label>
                            <div class="input-group">
                                <span class="input-group-text">฿</span>
                                <input type="text" id="remaining_balance_display" class="form-control bg-light" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="m-0"><i class="bi bi-file-earmark-text me-2"></i> ข้อมูลใบเสนอราคาที่เกี่ยวข้อง</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="bg-light" style="width: 20%">เลขที่ใบเสนอราคา</th>
                            <td>{{ $invoice->quotation->quotation_number }}</td>
                            <th class="bg-light" style="width: 20%">ลูกค้า</th>
                            <td>{{ $invoice->quotation->customer_company }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">ที่อยู่</th>
                            <td colspan="3">{{ $invoice->quotation->customer_address }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">เงื่อนไขการชำระ</th>
                            <td>{{ $invoice->quotation->payment_terms }}</td>
                            <th class="bg-light">วันที่ใบเสนอราคา</th>
                            <td>{{ $invoice->quotation->quotation_date instanceof \Carbon\Carbon ? $invoice->quotation->quotation_date->format('d/m/Y') : $invoice->quotation->quotation_date }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                <i class="bi bi-x-lg me-1"></i> ยกเลิก
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> บันทึกการเปลี่ยนแปลง
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function calculatePayment() {
    const totalAmount = {{ $invoice->total_amount }};
    const percentage = document.querySelector('input[name="payment_percentage"]').value;
    const paymentAmount = totalAmount * (percentage / 100);
    const remainingBalance = totalAmount - paymentAmount;

    // Update displayed values (formatted)
    document.querySelector('#payment_amount_display').value = 
        paymentAmount.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.querySelector('#remaining_balance_display').value = 
        remainingBalance.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Update hidden fields with actual values (not formatted) for form submission
    document.querySelector('#actual_payment_amount').value = paymentAmount.toFixed(2);
    document.querySelector('#actual_remaining_balance').value = remainingBalance.toFixed(2);
}

function validateForm() {
    const percentage = parseFloat(document.querySelector('input[name="payment_percentage"]').value);
    if (isNaN(percentage) || percentage < 0 || percentage > 100) {
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด',
            text: 'เปอร์เซ็นต์การชำระต้องอยู่ระหว่าง 0 ถึง 100',
        });
        return false;
    }
    
    // Update hidden fields with actual values before submission
    calculatePayment();
    return true;
}

// คำนวณค่าเริ่มต้นและตั้งค่าสถานะ
document.addEventListener('DOMContentLoaded', function() {
    calculatePayment();
    
    // สถานะแสดงสีที่กำหนด
    const statusSelect = document.querySelector('select[name="status_id"]');
    if (statusSelect) {
        // ตั้งค่าเริ่มต้นสำหรับตัวเลือกที่เลือกในขณะนี้
        if (statusSelect.selectedIndex > 0) {
            const option = statusSelect.options[statusSelect.selectedIndex];
            const color = option.dataset.color;
            statusSelect.style.backgroundColor = color;
            statusSelect.style.color = getContrastColor(color);
        }
        
        // ตั้งค่าสีสำหรับตัวเลือกทั้งหมด
        const options = statusSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value) {
                const statusObj = {!! json_encode($jobStatuses) !!}.find(s => s.id == option.value);
                if (statusObj && statusObj.color) {
                    option.style.backgroundColor = statusObj.color;
                    option.style.color = getContrastColor(statusObj.color);
                }
            }
        });
        
        // ปรับปรุงสีเมื่อมีการเปลี่ยนสถานะ
        statusSelect.addEventListener('change', function() {
            if (this.selectedIndex > 0) {
                const option = this.options[this.selectedIndex];
                const color = option.dataset.color;
                this.style.backgroundColor = color;
                this.style.color = getContrastColor(color);
            } else {
                this.style.backgroundColor = '';
                this.style.color = '';
            }
        });
    }
});

function getContrastColor(hexColor) {
    // แปลงสี hex เป็น RGB
    const r = parseInt(hexColor.substr(1,2), 16);
    const g = parseInt(hexColor.substr(3,2), 16);
    const b = parseInt(hexColor.substr(5,2), 16);
    
    // คำนวณความสว่าง
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    
    // ถ้าสีพื้นหลังสว่าง ใช้ตัวอักษรสีดำ มิฉะนั้นใช้สีขาว
    return brightness > 128 ? '#000000' : '#FFFFFF';
}
</script>
@endpush

@push('styles')
<style>
.form-label {
    margin-bottom: 0.3rem;
}
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}
.table th {
    font-weight: 600;
    background-color: rgba(0, 0, 0, 0.03);
}
.form-group {
    margin-bottom: 1rem;
}
.form-select option {
    padding: 8px;
}
.alert {
    border-left: 4px solid;
}
.alert-success {
    border-left-color: #198754;
}
.alert-danger {
    border-left-color: #dc3545;
}
</style>
@endpush
@endsection