@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Quotation</h1>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('quotations.store') }}" method="POST">
        @csrf

        <!-- Seller (Company Info) -->
        <h4>Seller (Company Info)</h4>
        <div class="mb-3">
            <label>Company</label>
            <select name="seller_company" class="form-control" id="seller_company" required>
                <option value="">-- Select Company --</option>
                @foreach($companies as $company)
                    <option value="{{ $company->company_name }}"
                        data-address="{{ $company->address }}"
                        data-phone="{{ $company->phone }}"
                        data-fax="{{ $company->fax }}"
                        data-line="{{ $company->line }}"
                        data-email="{{ $company->email }}"
                        {{ old('seller_company') == $company->company_name ? 'selected' : '' }}>
                        {{ $company->company_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="seller_address" id="seller_address" class="form-control">{{ old('seller_address') }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label>Phone</label>
                <input type="text" name="seller_phone" id="seller_phone" class="form-control" value="{{ old('seller_phone') }}">
            </div>
            <div class="col">
                <label>Fax</label>
                <input type="text" name="seller_fax" id="seller_fax" class="form-control" value="{{ old('seller_fax') }}">
            </div>
            <div class="col">
                <label>LINE</label>
                <input type="text" name="seller_line" id="seller_line" class="form-control" value="{{ old('seller_line') }}">
            </div>
            <div class="col">
                <label>Email</label>
                <input type="email" name="seller_email" id="seller_email" class="form-control" value="{{ old('seller_email') }}">
            </div>
        </div>

        <hr>

        <!-- Customer Info -->
        <h4>Customer Info</h4>
        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" class="form-control" id="customer_id">
                <option value="">-- Select Customer --</option>
                @foreach($customers as $cust)
                    <option value="{{ $cust->id }}"
                        data-contact="{{ $cust->contact_name }}"
                        data-address="{{ $cust->address }}"
                        data-phone="{{ $cust->phone }}"
                        data-fax="{{ $cust->fax }}"
                        data-email="{{ $cust->email }}"
                    >
                        {{ $cust->companyname }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Customer Contact Name (Auto-Filled)</label>
            <input type="text" name="customer_contact_name" id="customer_contact_name" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label>Customer Address</label>
            <textarea name="customer_address" id="customer_address" class="form-control" readonly></textarea>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label>Phone</label>
                <input type="text" name="customer_phone" id="customer_phone" class="form-control" readonly>
            </div>
            <div class="col">
                <label>Fax</label>
                <input type="text" name="customer_fax" id="customer_fax" class="form-control" readonly>
            </div>
            <div class="col">
                <label>Email</label>
                <input type="email" name="customer_email" id="customer_email" class="form-control" readonly>
            </div>
        </div>

        <hr>

        <!-- Quotation Info -->
        <h4>Quotation Info</h4>
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="quotation_date" class="form-control" value="{{ old('quotation_date') }}" required>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label>Your Ref</label>
                <input type="text" name="your_ref" class="form-control" value="{{ old('your_ref') }}">
            </div>
            <div class="col">
                <label>Our Ref</label>
                <input type="text" name="our_ref" class="form-control" value="{{ old('our_ref') }}">
            </div>
        </div>

        <hr>

        <!-- Status -->
        <h4>Status</h4>
        <div class="mb-3">
            <label for="status_id" class="form-label">Job Status</label>
            <select name="status_id" id="status_id" class="form-control">
                <option value="">-- Select Status --</option>
                @foreach($jobStatuses as $status)
                    <option value="{{ $status->id }}" 
                            {{ old('status_id') == $status->id ? 'selected' : '' }}
                            style="background-color: {{ $status->color }}; color: #fff;">
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <hr>

        <!-- Items -->
        <h4>Items</h4>
        <div id="items-wrapper">
            <!-- ตัวอย่างแถวที่ 1 -->
            <div class="row mb-2">
                <div class="col-md-1">
                    <label>No</label>
                    <input type="text" name="items[0][item_no]" class="form-control" value="1">
                </div>
                <div class="col-md-4">
                    <label>Product</label>
                    <select name="items[0][product_id]" class="form-control product-select">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                data-price="{{ $product->price }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Qty</label>
                    <input type="number" name="items[0][quantity]" class="form-control quantity" value="1">
                </div>
                <div class="col-md-2">
                    <label>Unit Price</label>
                    <input type="number" step="0.01" name="items[0][unit_price]" class="form-control unit-price" value="0">
                </div>
                <div class="col-md-2">
                    <label>Total</label>
                    <input type="number" step="0.01" name="items[0][net_price]" class="form-control net-price" value="0" readonly>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addItem()">+ Add Item</button>

        <!-- Grand Total -->
        <div class="mb-3 text-end">
            <strong>Grand Total: </strong>
            <span id="grand_total">0.00</span>
        </div>

        <hr>

        <!-- Conditions -->
        <h4>Conditions</h4>
        <div class="row mb-3">
            <div class="col">
                <label>Delivery</label>
                <input type="text" name="delivery" class="form-control" value="{{ old('delivery') }}">
            </div>
            <div class="col">
                <label>Warranty</label>
                <input type="text" name="warranty" class="form-control" value="{{ old('warranty') }}">
            </div>
            <div class="col">
                <label>Validity</label>
                <input type="text" name="validity" class="form-control" value="{{ old('validity') }}">
            </div>
            <div class="col">
                <label>Payment</label>
                <select name="payment" class="form-control" required>
                    <option value="">-- Select Payment Status --</option>
                    @foreach($payment_statuses as $status)
                        <option value="{{ $status->name }}" {{ old('payment') == $status->name ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr>

        <!-- Signatures -->
        <h4>Signatures</h4>
        <div class="row mb-3">
            <div class="col">
                <label>Prepared By</label>
                <select name="prepared_by" class="form-control">
                    <option value="">-- Select Staff --</option>
                    @foreach($sales_employees as $employee)
                        @php
                            $fullname = $employee->first_name . ' ' . $employee->last_name;
                        @endphp
                        <option value="{{ $fullname }}" {{ old('prepared_by') == $fullname ? 'selected' : '' }}>
                            {{ $fullname }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label>Sales Engineer</label>
                <select name="sales_engineer" class="form-control">
                    <option value="">-- Select Sales Person --</option>
                    @foreach($sales_employees as $employee)
                        @php
                            $fullname = $employee->first_name . ' ' . $employee->last_name;
                        @endphp
                        <option value="{{ $fullname }}" {{ old('sales_engineer') == $fullname ? 'selected' : '' }}>
                            {{ $fullname }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Quotation</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Seller auto-fill
    let sellerCompanySelect = document.getElementById("seller_company");
    if(sellerCompanySelect) {
        sellerCompanySelect.addEventListener("change", function(){
            let sel = this.options[this.selectedIndex];
            document.getElementById("seller_address").value = sel.getAttribute("data-address") || "";
            document.getElementById("seller_phone").value   = sel.getAttribute("data-phone") || "";
            document.getElementById("seller_fax").value     = sel.getAttribute("data-fax") || "";
            document.getElementById("seller_line").value    = sel.getAttribute("data-line") || "";
            document.getElementById("seller_email").value   = sel.getAttribute("data-email") || "";
        });
    }

    // Customer auto-fill (เลือก Customer จาก customer_id)
    let customerSelect = document.getElementById("customer_id");
    if(customerSelect) {
        customerSelect.addEventListener("change", function(){
            let sel = this.options[this.selectedIndex];
            document.getElementById("customer_contact_name").value = sel.getAttribute("data-contact") || "";
            document.getElementById("customer_address").value      = sel.getAttribute("data-address") || "";
            document.getElementById("customer_phone").value        = sel.getAttribute("data-phone") || "";
            document.getElementById("customer_fax").value          = sel.getAttribute("data-fax") || "";
            document.getElementById("customer_email").value        = sel.getAttribute("data-email") || "";
        });
    }

    // ผูก event กับแถวแรกของ Items
    let firstRow = document.querySelector("#items-wrapper .row");
    attachItemEvents(firstRow);
});

// ฟังก์ชันสร้างแถว Item ใหม่
function addItem(){
    let wrapper = document.getElementById('items-wrapper');
    let rowCount = wrapper.querySelectorAll('.row.mb-2').length;
    let row = document.createElement('div');
    row.classList.add('row','mb-2');
    row.innerHTML = `
        <div class="col-md-1">
            <label>No</label>
            <input type="text" name="items[${rowCount}][item_no]" class="form-control" value="${rowCount + 1}">
        </div>
        <div class="col-md-4">
            <label>Product</label>
            <select name="items[${rowCount}][product_id]" class="form-control product-select">
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        data-price="{{ $product->price }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Qty</label>
            <input type="number" name="items[${rowCount}][quantity]" class="form-control quantity" value="1">
        </div>
        <div class="col-md-2">
            <label>Unit Price</label>
            <input type="number" step="0.01" name="items[${rowCount}][unit_price]" class="form-control unit-price" value="0">
        </div>
        <div class="col-md-2">
            <label>Total</label>
            <input type="number" step="0.01" name="items[${rowCount}][net_price]" class="form-control net-price" value="0" readonly>
        </div>
    `;
    wrapper.appendChild(row);
    attachItemEvents(row);
    updateGrandTotal();
}

function attachItemEvents(row){
    let productSelect = row.querySelector('.product-select');
    let qtyInput      = row.querySelector('.quantity');
    let priceInput    = row.querySelector('.unit-price');

    // เมื่อเปลี่ยนสินค้า → ตั้ง Unit Price + คำนวณ Total
    productSelect?.addEventListener('change', function(){
        let selectedOption = this.options[this.selectedIndex];
        priceInput.value = selectedOption.getAttribute('data-price') || 0;
        updateNetPrice(row);
    });

    qtyInput?.addEventListener('input', function(){
        updateNetPrice(row);
    });
    priceInput?.addEventListener('input', function(){
        updateNetPrice(row);
    });
}

function updateNetPrice(row){
    let qty = parseFloat(row.querySelector('.quantity').value) || 0;
    let price = parseFloat(row.querySelector('.unit-price').value) || 0;
    let net = row.querySelector('.net-price');
    net.value = (qty * price).toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal(){
    let sum = 0;
    document.querySelectorAll('.net-price').forEach(function(el){
        sum += parseFloat(el.value) || 0;
    });
    document.getElementById('grand_total').innerText = sum.toFixed(2);
}
</script>
@endsection