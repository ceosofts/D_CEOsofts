@extends('layouts.app')

@section('title', 'Show Quotation')

@section('content')
<div class="container" style="max-width: 900px;">
    <!-- Seller Header -->
    <div class="row">
        <div class="col-md-8">
            <h5>{{ $quotation->seller_company }}</h5>
            <p>{{ $quotation->seller_address }}</p>
            @if($seller)
                <p>Tax ID: {{ $seller->tax_id }} | Branch: {{ $seller->branch_description }}</p>  <!-- รวม Tax ID และ Branch ไว้ในบรรทัดเดียวกัน -->
            @endif
            <p>Tel: {{ $quotation->seller_phone }} | Fax: {{ $quotation->seller_fax }} </p>
            <p>Email: {{ $quotation->seller_email }} | LINE: {{ $quotation->seller_line }}</p>
        </div>
        <div class="col-md-4 text-end">
            <h4>Quotation</h4>
            <p><strong>No:</strong> {{ $quotation->quotation_number }}</p>
            <p><strong>Date:</strong> {{ $quotation->quotation_date }}</p>
        </div>
    </div>
    <hr>

    <!-- Customer Info -->
    <div class="row mb-2">
        <div class="col-md-6">
            <strong>To:</strong> {{ $quotation->customer_company }}<br>
            <strong>Contact:</strong> {{ $quotation->customer_contact_name }}<br>
            <strong>Address:</strong> {{ $quotation->customer_address }}<br>
            @if($customer)
                <strong>Tax ID:</strong> {{ $customer->taxid }} | <strong>Branch:</strong> {{ $customer->branch }}<br>
            @endif
            <strong>Tel:</strong> {{ $quotation->customer_phone }} | <strong>Fax:</strong> {{ $quotation->customer_fax }}<br>
            <strong>Email:</strong> {{ $quotation->customer_email }}
        </div>
        <div class="col-md-6 text-end">
            <p><strong>Your Ref:</strong> {{ $quotation->your_ref }}</p>
            <p><strong>Our Ref:</strong> {{ $quotation->our_ref }}</p>
        </div>
    </div>

    <p>Thank you for the opportunity to provide you a quotation for the following product(s).</p>

    <!-- Items Table -->
    <table class="table table-bordered">
        <thead>
            <tr class="text-center fw-bold">
                <th style="width:50px;">No</th>
                <th>Product/Description</th>
                <th style="width:80px;">Q'ty</th>
                <th style="width:120px;">Unit Price</th>
                <th style="width:120px;">Net (THB)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr>
                <td class="text-center">{{ $item->item_no }}</td>
                <td>
                    @if($item->product)
                        {{ $item->product->name }}<br>
                        <small>{{ $item->description }}</small>
                    @else
                        {{ $item->description }}
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($item->unit_price,2) }}</td>
                <td class="text-end">{{ number_format($item->net_price,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- คำนวณ Sub-Total, VAT, Grand Total -->
    @php
        // สมมติว่า VAT = 7% ของ total_amount
        $vat = $quotation->total_amount * 0.07;
        $grandTotal = $quotation->total_amount + $vat;
    @endphp

    <div class="row">
        <div class="col-md-8">
            <!-- amount_in_words เก็บคำอ่านยอดเงิน เช่น ONE HUNDRED ONLY -->
            <strong>{{ $quotation->amount_in_words }}</strong>
        </div>
        <div class="col-md-4 text-end">
            <p>Sub-Total: {{ number_format($quotation->total_amount, 2) }} THB</p>
            <p>VAT (7%): {{ number_format($vat, 2) }} THB</p>
            <h4>Grand Total: {{ number_format($grandTotal, 2) }} THB</h4>
        </div>
    </div>
    <hr>

    <!-- Conditions -->
    <div class="row">
        <div class="col-md-3">
            <strong>Delivery:</strong> {{ $quotation->delivery }}
        </div>
        <div class="col-md-3">
            <strong>Warranty:</strong> {{ $quotation->warranty }}
        </div>
        <div class="col-md-3">
            <strong>Validity:</strong> {{ $quotation->validity }}
        </div>
        <div class="col-md-3">
            <strong>Payment Terms:</strong>
            @if($quotation->payment)
                <span class="badge bg-info text-dark">{{ $quotation->payment }}</span>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </div>
    </div>
    <hr>

    <!-- Signatures -->
    <div class="row mt-3">
        <div class="col-md-4">
            <p><strong>Customer Confirm By:</strong><br><br><br>__________________________</p>
        </div>
        <div class="col-md-4 text-center">
            <p><strong>Prepared By:</strong><br>{{ $quotation->prepared_by }}</p>
        </div>
        <div class="col-md-4 text-end">
            <p><strong>Sales Engineer:</strong><br>{{ $quotation->sales_engineer }}</p>
        </div>
    </div>

    <!-- Buttons -->
    <div class="row mt-4">
        <div class="col-md-6">
            <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-warning">Edit</a>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('quotations.export', $quotation->id) }}" class="btn btn-primary">Export to PDF</a>
        </div>
    </div>
</div>
@endsection