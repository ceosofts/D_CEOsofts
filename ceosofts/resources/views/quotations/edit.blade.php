@extends('layouts.app')

@section('title', 'Edit Quotation #' . $quotation->quotation_number)

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Quotation #{{ $quotation->quotation_number }}</h1>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('quotations.update', $quotation->id) }}" method="POST">
        @csrf
        @method('PUT')

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
                        {{ old('seller_company', $quotation->seller_company) == $company->company_name ? 'selected' : '' }}>
                        {{ $company->company_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="seller_address" id="seller_address" class="form-control">{{ old('seller_address', $quotation->seller_address) }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label>Phone</label>
                <input type="text" name="seller_phone" id="seller_phone" class="form-control"
                       value="{{ old('seller_phone', $quotation->seller_phone) }}">
            </div>
            <div class="col">
                <label>Fax</label>
                <input type="text" name="seller_fax" id="seller_fax" class="form-control"
                       value="{{ old('seller_fax', $quotation->seller_fax) }}">
            </div>
            <div class="col">
                <label>LINE</label>
                <input type="text" name="seller_line" id="seller_line" class="form-control"
                       value="{{ old('seller_line', $quotation->seller_line) }}">
            </div>
            <div class="col">
                <label>Email</label>
                <input type="email" name="seller_email" id="seller_email" class="form-control"
                       value="{{ old('seller_email', $quotation->seller_email) }}">
            </div>
        </div>

        <hr>

        <!-- Customer Info -->
        <h4>Customer Info</h4>
        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" class="form-control" id="customer_id" required>
                <option value="">-- Select Customer --</option>
                @foreach($customers as $cust)
                    <option value="{{ $cust->id }}"
                        data-contact="{{ $cust->contact_name }}"
                        data-address="{{ $cust->address }}"
                        data-phone="{{ $cust->phone }}"
                        data-fax="{{ $cust->fax }}"
                        data-email="{{ $cust->email }}"
                        {{ old('customer_id', $quotation->customer_id) == $cust->id ? 'selected' : '' }}>
                        {{ $cust->companyname }}
                    </option>
                @endforeach
            </select>
        </div>
        <!-- ฟิลด์ auto-fill Customer Info -->
        <div class="mb-3">
            <label>Customer Contact Name (Auto-Filled)</label>
            <input type="text" name="customer_contact_name" id="customer_contact_name" class="form-control" readonly
                   value="{{ old('customer_contact_name', $quotation->customer_contact_name) }}">
        </div>
        <div class="mb-3">
            <label>Customer Address</label>
            <textarea name="customer_address" id="customer_address" class="form-control" readonly>{{ old('customer_address', $quotation->customer_address) }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label>Phone</label>
                <input type="text" name="customer_phone" id="customer_phone" class="form-control" readonly
                       value="{{ old('customer_phone', $quotation->customer_phone) }}">
            </div>
            <div class="col">
                <label>Fax</label>
                <input type="text" name="customer_fax" id="customer_fax" class="form-control" readonly
                       value="{{ old('customer_fax', $quotation->customer_fax) }}">
            </div>
            <div class="col">
                <label>Email</label>
                <input type="email" name="customer_email" id="customer_email" class="form-control" readonly
                       value="{{ old('customer_email', $quotation->customer_email) }}">
            </div>
        </div>

        <hr>

        <!-- Quotation Info -->
        <h4>Quotation Info</h4>
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="quotation_date" class="form-control" required
                   value="{{ old('quotation_date', $quotation->quotation_date) }}">
        </div>
        <div class="row mb-3">
            <div class="col">
                <label>Your Ref</label>
                <input type="text" name="your_ref" class="form-control"
                       value="{{ old('your_ref', $quotation->your_ref) }}">
            </div>
            <div class="col">
                <label>Our Ref</label>
                <input type="text" name="our_ref" class="form-control"
                       value="{{ old('our_ref', $quotation->our_ref) }}">
            </div>
        </div>

        <hr>

        <!-- Items -->
        <h4>Items</h4>
        <div id="items-wrapper">
            @foreach($quotation->items as $i => $itm)
            <div class="row mb-2 item-row">
                <div class="col-md-1">
                    <label>No</label>
                    <input type="text" name="items[{{ $i }}][item_no]" class="form-control"
                           value="{{ old("items.$i.item_no", $itm->item_no) }}">
                </div>
                <div class="col-md-4">
                    <label>Product</label>
                    <select name="items[{{ $i }}][product_id]" class="form-control product-select">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}"
                                data-price="{{ $p->price }}"
                                @if(old("items.$i.product_id", $itm->product_id) == $p->id) selected @endif>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Qty</label>
                    <input type="number" name="items[{{ $i }}][quantity]" class="form-control quantity"
                           value="{{ old("items.$i.quantity", $itm->quantity) }}">
                </div>
                <div class="col-md-2">
                    <label>Unit Price</label>
                    <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control unit-price"
                           value="{{ old("items.$i.unit_price", $itm->unit_price) }}">
                </div>
                <div class="col-md-2">
                    <label>Total</label>
                    <input type="number" step="0.01" name="items[{{ $i }}][net_price]" class="form-control net-price"
                           value="{{ old("items.$i.net_price", $itm->net_price) }}" readonly>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                </div>
            </div>
            @endforeach
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
                <input type="text" name="delivery" class="form-control"
                       value="{{ old('delivery', $quotation->delivery) }}">
            </div>
            <div class="col">
                <label>Warranty</label>
                <input type="text" name="warranty" class="form-control"
                       value="{{ old('warranty', $quotation->warranty) }}">
            </div>
            <div class="col">
                <label>Validity</label>
                <input type="text" name="validity" class="form-control"
                       value="{{ old('validity', $quotation->validity) }}">
            </div>
            <div class="col">
                <label>Payment</label>
                <input type="text" name="payment" class="form-control"
                       value="{{ old('payment', $quotation->payment) }}">
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
                    @foreach($sales_employees as $emp)
                        @php
                            $fullname = $emp->first_name . ' ' . $emp->last_name;
                        @endphp
                        <option value="{{ $fullname }}"
                            {{ old('prepared_by', $quotation->prepared_by) == $fullname ? 'selected' : '' }}>
                            {{ $fullname }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label>Sales Engineer</label>
                <select name="sales_engineer" class="form-control">
                    <option value="">-- Select Sales Person --</option>
                    @foreach($sales_employees as $emp)
                        @php
                            $fullname = $emp->first_name . ' ' . $emp->last_name;
                        @endphp
                        <option value="{{ $fullname }}"
                            {{ old('sales_engineer', $quotation->sales_engineer) == $fullname ? 'selected' : '' }}>
                            {{ $fullname }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Quotation</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Seller auto-fill (เรียกทันทีที่หน้าโหลด)
    let sellerCompanySelect = document.getElementById("seller_company");
    if(sellerCompanySelect) {
        autoFillSeller();
        sellerCompanySelect.addEventListener("change", autoFillSeller);
    }

    // Customer auto-fill (เรียกทันทีที่หน้าโหลด)
    let customerSelect = document.getElementById("customer_id");
    if(customerSelect) {
        autoFillCustomer();
        customerSelect.addEventListener("change", autoFillCustomer);
    }

    // ผูก event กับแถว Items ที่มีอยู่แล้ว
    document.querySelectorAll('.item-row').forEach(function(row){
        attachItemEvents(row);
    });
    updateGrandTotal();
    updateItemNumbers();

    // ปุ่ม Remove สำหรับแถวเดิม
    document.querySelectorAll('.remove-item').forEach(function(btn){
        btn.addEventListener('click', function(){
            btn.closest('.item-row').remove();
            updateGrandTotal();
            updateItemNumbers();
        });
    });
});

function autoFillSeller(){
    let sellerCompanySelect = document.getElementById("seller_company");
    let sel = sellerCompanySelect.options[sellerCompanySelect.selectedIndex];
    if(sel && sel.value){
        document.getElementById("seller_address").value = sel.getAttribute("data-address") || "";
        document.getElementById("seller_phone").value   = sel.getAttribute("data-phone") || "";
        document.getElementById("seller_fax").value     = sel.getAttribute("data-fax") || "";
        document.getElementById("seller_line").value    = sel.getAttribute("data-line") || "";
        document.getElementById("seller_email").value   = sel.getAttribute("data-email") || "";
    }
}

function autoFillCustomer(){
    let customerSelect = document.getElementById("customer_id");
    let sel = customerSelect.options[customerSelect.selectedIndex];
    if(sel && sel.value){
        document.getElementById("customer_contact_name").value = sel.getAttribute("data-contact") || "";
        document.getElementById("customer_address").value      = sel.getAttribute("data-address") || "";
        document.getElementById("customer_phone").value        = sel.getAttribute("data-phone") || "";
        document.getElementById("customer_fax").value          = sel.getAttribute("data-fax") || "";
        document.getElementById("customer_email").value        = sel.getAttribute("data-email") || "";
    }
}

// ใช้ index จากจำนวน items ที่มีอยู่แล้ว
let idx = {{ count($quotation->items) }};

function addItem(){
    let wrapper = document.getElementById('items-wrapper');
    let rowCount = wrapper.querySelectorAll('.item-row').length;
    let row = document.createElement('div');
    row.classList.add('row','mb-2','item-row');
    row.innerHTML = `
        <div class="col-md-1">
            <label>No</label>
            <input type="text" name="items[\${rowCount}][item_no]" class="form-control" value="">
        </div>
        <div class="col-md-4">
            <label>Product</label>
            <select name="items[\${rowCount}][product_id]" class="form-control product-select">
                <option value="">-- Select Product --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}"
                        data-price="{{ $p->price }}">
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Qty</label>
            <input type="number" name="items[\${rowCount}][quantity]" class="form-control quantity" value="1">
        </div>
        <div class="col-md-2">
            <label>Unit Price</label>
            <input type="number" step="0.01" name="items[\${rowCount}][unit_price]" class="form-control unit-price" value="0">
        </div>
        <div class="col-md-2">
            <label>Total</label>
            <input type="number" step="0.01" name="items[\${rowCount}][net_price]" class="form-control net-price" value="0" readonly>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
        </div>
    `.replace(/\${rowCount}/g, rowCount);

    wrapper.appendChild(row);
    attachItemEvents(row);

    // ปุ่ม Remove สำหรับแถวใหม่
    row.querySelector('.remove-item').addEventListener('click', function(){
        row.remove();
        updateGrandTotal();
        updateItemNumbers();
    });

    idx++;
    updateItemNumbers();
}

// ผูก event ให้กับ Dropdown Product, Qty, และ Unit Price
function attachItemEvents(row){
    let productSelect = row.querySelector('.product-select');
    let qtyInput      = row.querySelector('.quantity');
    let priceInput    = row.querySelector('.unit-price');

    productSelect?.addEventListener('change', function(){
        let sel = this.options[this.selectedIndex];
        priceInput.value = sel.getAttribute('data-price') || 0;
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
    let qty   = parseFloat(row.querySelector('.quantity').value) || 0;
    let price = parseFloat(row.querySelector('.unit-price').value) || 0;
    let net   = row.querySelector('.net-price');
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

// จัดลำดับหมายเลข No ใหม่ทุกครั้งที่มีการเพิ่ม/ลบแถว
function updateItemNumbers(){
    let rows = document.querySelectorAll('.item-row');
    rows.forEach(function(row, index){
        let noField = row.querySelector('input[name^="items"][name$="[item_no]"]');
        if(noField){
            noField.value = index + 1;
        }
    });
}
</script>
@endsection