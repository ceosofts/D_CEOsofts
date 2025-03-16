@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Create Invoice from Quotation #{{ $quotation->quotation_number }}</h1>
        <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Quotation
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">

        <!-- เพิ่ม debug information -->
        @if(config('app.debug'))
            <div class="d-none">
                <p>Form Action: {{ route('invoices.store') }}</p>
                <p>Quotation ID: {{ $quotation->id }}</p>
                <p>CSRF Token: {{ csrf_token() }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Invoice Details Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Invoice Details</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Invoice Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary text-white">
                                        <i class="bi bi-hash"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light non-currency" value="{{ $invoiceNumber }}" readonly>
                                    <small class="form-text text-muted mt-1">เลขที่ใบแจ้งหนี้จะถูกสร้างอัตโนมัติ</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Payment Terms</label>
                                <select name="payment_terms" class="form-select" required onchange="updateDueDate(this)">
                                    <option value="">-- Select Payment Terms --</option>
                                    @foreach($paymentStatuses as $status)
                                        <option value="{{ $status->name }}" 
                                                data-days="{{ $status->days ?? 0 }}">
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Due Date</label>
                                <input type="date" name="due_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Status</label>
                                <select name="status_id" class="form-select" required>
                                    <option value="">-- Select Status --</option>
                                    @foreach($jobStatuses as $status)
                                        <option value="{{ $status->id }}" 
                                                data-color="{{ $status->color }}">
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Payment Details</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Total Amount (from Quotation)</label>
                                <div class="input-group">
                                    <span class="input-group-text">฿</span>
                                    <input type="text" class="form-control" 
                                           value="{{ number_format($quotation->total_amount, 2) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Payment Percentage</label>
                                <div class="input-group">
                                    <input type="number" name="payment_percentage" class="form-control" 
                                           min="0" max="100" step="0.01" value="50" required
                                           onchange="calculatePayment()">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">฿</span>
                                    <input type="text" name="payment_amount" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Remaining Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text">฿</span>
                                    <input type="text" name="remaining_balance" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Customer Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Company:</th>
                                <td>{{ $quotation->customer_company }}</td>
                            </tr>
                            <tr>
                                <th>Contact:</th>
                                <td>{{ $quotation->customer_contact_name }}</td>
                            </tr>
                            <tr>
                                <th>Reference:</th>
                                <td>{{ $quotation->your_ref }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-save"></i> Create Invoice
                        </button>
                        <a href="{{ route('quotations.show', $quotation) }}" 
                           class="btn btn-secondary w-100">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.form-select option[data-color] {
    padding: 5px;
}

.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-select.is-invalid {
    border-color: #dc3545;
    padding-right: 4.125rem;
}
</style>
@endpush

@push('scripts')
<script>
function calculatePayment() {
    const totalAmount = {{ $quotation->total_amount }};
    const percentage = document.querySelector('input[name="payment_percentage"]').value;
    const paymentAmount = totalAmount * (percentage / 100);
    const remainingBalance = totalAmount - paymentAmount;

    document.querySelector('input[name="payment_amount"]').value = 
        paymentAmount.toFixed(2);
    document.querySelector('input[name="remaining_balance"]').value = 
        remainingBalance.toFixed(2);
}

function updateDueDate(select) {
    const days = select.options[select.selectedIndex].dataset.days;
    const invoiceDate = document.querySelector('input[name="invoice_date"]').value;
    
    if (invoiceDate && days) {
        const dueDate = new Date(invoiceDate);
        dueDate.setDate(dueDate.getDate() + parseInt(days));
        document.querySelector('input[name="due_date"]').value = 
            dueDate.toISOString().split('T')[0];
    }
}

// Color the status options
document.querySelector('select[name="status_id"]').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const color = option.dataset.color;
    this.style.backgroundColor = color;
    this.style.color = '#fff';
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculatePayment();
    // Set initial colors for status
    const statusSelect = document.querySelector('select[name="status_id"]');
    if (statusSelect.value) {
        const option = statusSelect.options[statusSelect.selectedIndex];
        statusSelect.style.backgroundColor = option.dataset.color;
        statusSelect.style.color = '#fff';
    }

    // Initial calculation
    calculatePayment();

    // Set initial due date if payment terms is selected
    const termsSelect = document.querySelector('select[name="payment_terms"]');
    if (termsSelect.value) {
        updateDueDate(termsSelect);
    }

    // Format currency inputs - ไม่รวมช่อง Invoice Number
    const currencyInputs = document.querySelectorAll('input[readonly][class*="form-control"]:not(.non-currency)');
    currencyInputs.forEach(input => {
        if (input.value) {
            input.value = parseFloat(input.value.replace(/[^0-9.-]+/g, ""))
                .toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
        }
    });
});

// Watch invoice date changes
document.querySelector('input[name="invoice_date"]').addEventListener('change', function() {
    const termsSelect = document.querySelector('select[name="payment_terms"]');
    if (termsSelect.value) {
        updateDueDate(termsSelect);
    }
});

document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields');
        return;
    }

    // Calculate final values before submission
    calculatePayment();

    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';

    // Log form data for debugging
    const formData = new FormData(this);
    console.log('Submitting form data:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    // Submit the form
    this.submit();
});
</script>
@endpush
@endsection