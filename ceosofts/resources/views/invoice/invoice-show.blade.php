<!-- filepath: /Users/iwasbornforthis/MyProject/D_CEOsofts/ceosofts/resources/views/invoice/invoice-show.blade.php -->
@extends('layouts.app')

@section('title', 'รายละเอียดใบแจ้งหนี้')

@section('content')
<div class="container" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt me-2 text-primary"></i> ใบแจ้งหนี้</h2>
        <div>
            @can('edit invoice')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-fill me-1"></i> แก้ไข
                </a>
            @endcan
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf-fill me-1"></i> ส่งออก PDF
            </a>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> กลับ
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Seller Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ $invoice->quotation->seller_company }}</h5>
                    <p class="mb-1">{{ $invoice->quotation->seller_address }}</p>
                    <p class="mb-1">เลขประจำตัวผู้เสียภาษี: {{ $invoice->quotation->seller_tax_id ?? '-' }} | สาขา: {{ $invoice->quotation->seller_branch ?? 'สำนักงานใหญ่' }}</p>
                    <p class="mb-1">Tel: {{ $invoice->quotation->seller_phone }} | Fax: {{ $invoice->quotation->seller_fax }}</p>
                    <p class="mb-1">Email: {{ $invoice->quotation->seller_email }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <h4 class="text-primary fw-bold">ใบแจ้งหนี้</h4>
                    <p class="mb-1"><strong>เลขที่:</strong> {{ $invoice->invoice_number }}</p>
                    <p class="mb-1"><strong>วันที่:</strong> {{ is_object($invoice->invoice_date) ? $invoice->invoice_date->format('d/m/Y') : $invoice->invoice_date }}</p>
                    <p class="mb-1"><strong>ครบกำหนด:</strong> {{ is_object($invoice->due_date) ? $invoice->due_date->format('d/m/Y') : $invoice->due_date }}</p>
                    <p>
                        <strong>สถานะ:</strong> 
                        @if($invoice->status)
                            <span class="badge" style="background-color: {{ $invoice->status->color }}">
                                {{ $invoice->status->name }}
                            </span>
                        @else
                            <span class="badge bg-secondary">ไม่มีสถานะ</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-8">
                    <h6 class="fw-bold">ข้อมูลลูกค้า</h6>
                    <p class="mb-1"><strong>บริษัท:</strong> {{ $invoice->quotation->customer_company }}</p>
                    <p class="mb-1"><strong>ผู้ติดต่อ:</strong> {{ $invoice->quotation->customer_contact_name }}</p>
                    <p class="mb-1"><strong>ที่อยู่:</strong> {{ $invoice->quotation->customer_address }}</p>
                    <p class="mb-1"><strong>เบอร์โทร:</strong> {{ $invoice->quotation->customer_phone }}</p>
                    <p class="mb-1"><strong>อีเมล:</strong> {{ $invoice->quotation->customer_email }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <h6 class="fw-bold">ข้อมูลอ้างอิง</h6>
                    <p class="mb-1"><strong>อ้างอิงของคุณ:</strong> {{ $invoice->your_ref }}</p>
                    <p class="mb-1"><strong>อ้างอิงของเรา:</strong> {{ $invoice->our_ref }}</p>
                    <p class="mb-1"><strong>เลขที่ใบเสนอราคา:</strong> 
                        <a href="{{ route('quotations.show', $invoice->quotation) }}">
                            {{ $invoice->quotation->quotation_number }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 fw-bold"><i class="bi bi-cash-coin me-2"></i> ข้อมูลการชำระเงิน</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="fw-bold">{{ $invoice->amount_in_words }}</h5>
                    <p><strong>เงื่อนไขการชำระเงิน:</strong> {{ $invoice->payment_terms }}</p>
                </div>
                <div class="col-md-4 text-end">
                    @php
                        // กำหนดยอดรวมก่อน VAT (ถ้าไม่มีในฐานข้อมูล ให้ใช้ 300)
                        $subtotal = $invoice->quotation->subtotal ?? $invoice->subtotal ?? 300;
                        
                        // คำนวณ VAT 7%
                        $vatAmount = $invoice->quotation->vat_amount ?? $invoice->vat_amount ?? ($subtotal * 0.07);
                        
                        // คำนวณยอดรวมสุทธิ
                        $totalAmount = $subtotal + $vatAmount;
                        
                        // คำนวณจำนวนเงินที่ต้องชำระจากยอดรวมสุทธิ
                        $paymentPercentage = $invoice->payment_percentage;
                        $paymentAmount = $totalAmount * ($paymentPercentage / 100);
                        
                        // คำนวณยอดคงเหลือจากยอดรวมสุทธิ
                        $remainingBalance = $totalAmount - $paymentAmount;
                    @endphp
                    
                    <table class="ms-auto">
                        <tr>
                            <td class="text-end pe-3"><strong>ยอดรวมก่อน VAT:</strong></td>
                            <td class="text-end">฿{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-3"><strong>VAT 7%:</strong></td>
                            <td class="text-end">฿{{ number_format($vatAmount, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td class="text-end pe-3 pb-2"><strong>ยอดรวมสุทธิ:</strong></td>
                            <td class="text-end pb-2">฿{{ number_format($totalAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-3 pt-2"><strong>เปอร์เซ็นต์การชำระ:</strong></td>
                            <td class="text-end pt-2">{{ number_format($invoice->payment_percentage, 2) }}%</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-3"><strong>จำนวนเงินที่ต้องชำระงวดนี้:</strong></td>
                            <td class="text-end">฿{{ number_format($paymentAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-3"><strong>ยอดคงเหลือ:</strong></td>
                            <td class="text-end">฿{{ number_format($remainingBalance, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 fw-bold"><i class="bi bi-list-check me-2"></i> รายการสินค้า</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        <th style="width:50px;">ลำดับ</th>
                        <th>รายการสินค้า/คำอธิบาย</th>
                        <th style="width:80px;">จำนวน</th>
                        <th style="width:120px;">ราคาต่อหน่วย</th>
                        <th style="width:120px;">ราคารวม</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->quotation->items as $item)
                    <tr>
                        <td class="text-center">{{ $item->item_no }}</td>
                        <td>
                            @if($item->product)
                                <strong>{{ $item->product->name }}</strong><br>
                                <small>{{ $item->description }}</small>
                            @else
                                {{ $item->description }}
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">฿{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">฿{{ number_format($item->net_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Signatures and Additional Info -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold"><i class="bi bi-person-check me-2"></i> การอนุมัติ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-center pb-4">
                            <p><strong>ลงชื่อผู้รับวางบิล:</strong></p>
                            <br></br>
                            <div class="signature-line mt-4"></div>
                            <p class="mt-2">วันที่: ___/___/_____</p>
                        </div>
                        <div class="col-md-6 text-center">
                            <p><strong>ลงชื่อผู้รับเงิน:</strong></p>
                            <br></br>
                            <div class="signature-line mt-4"></div>
                            <p class="mt-2">วันที่: ___/___/_____</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold"><i class="bi bi-info-circle me-2"></i> ข้อมูลการติดตาม</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="120">สร้างโดย:</th>
                            <td>{{ $invoice->creator ? $invoice->creator->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>สร้างเมื่อ:</th>
                            <td>{{ is_object($invoice->created_at) ? $invoice->created_at->format('d/m/Y H:i') : $invoice->created_at }}</td>
                        </tr>
                        <tr>
                            <th>แก้ไขโดย:</th>
                            <td>{{ $invoice->updater ? $invoice->updater->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>แก้ไขล่าสุด:</th>
                            <td>{{ is_object($invoice->updated_at) ? $invoice->updated_at->format('d/m/Y H:i') : $invoice->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}
.badge {
    padding: 5px 10px;
    font-weight: 500;
}
.table th {
    font-weight: 600;
    background-color: rgba(0, 0, 0, 0.03);
}
.signature-line {
    width: 100%;
    height: 1px;
    background-color: #000;
    margin: 0 auto;
}
.table-sm td, .table-sm th {
    padding: 0.5rem;
}
</style>
@endpush
@endsection