<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
    <style>
        /* กำหนดฟอนต์ THSarabunNew สำหรับการแสดงผล */
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
        }
        /* กำหนดฟอนต์เริ่มต้นสำหรับทุกองค์ประกอบ */
        * {
            font-family: 'THSarabunNew' , sans-serif !important;
        }
        /* กำหนดสไตล์สำหรับ body */
        body {
            font-size: 13pt; /* ขนาดตัวอักษร */
            line-height: 1.0; /* ระยะห่างระหว่างบรรทัด */
            padding: 15px; /* ระยะห่างภายใน */
        }
                /* กำหนดฟอนต์สำหรับภาษาไทย */
        .thai {
            font-family: 'THSarabunNew', sans-serif !important;
        }
        /* กำหนดสไตล์สำหรับหัวข้อ */
        h4, h5 {
            font-size: 11pt; /* ขนาดตัวอักษร */
            margin: 5px 0; /* ระยะห่างด้านบนและล่าง */
            font-weight: bold; /* ตัวหนา */
        }
        /* กำหนดสไตล์สำหรับย่อหน้า */
        p {
            margin: 3px 0; /* ระยะห่างด้านบนและล่าง */
        }
        /* กำหนดสไตล์สำหรับตัวหนา */
        strong {
            font-weight: bold; /* ตัวหนา */
            font-size: 10pt; /* ขนาดตัวอักษร */
        }
        /* กำหนดสไตล์สำหรับตาราง */
        table {
            width: 100%; /* ความกว้างเต็ม */
            border-collapse: collapse; /* รวมเส้นขอบ */
            margin: 5px 0; /* ระยะห่างด้านบนและล่าง */
        }
        /* กำหนดสไตล์สำหรับหัวตาราง */
        th {
            background-color: #f5f5f5; /* สีพื้นหลัง */
            font-weight: bold; /* ตัวหนา */
            font-size: 9pt; /* ขนาดตัวอักษร */
            padding: 5px; /* ระยะห่างภายใน */
            border: 0.5px solid #000; /* เส้นขอบ */
        }
        /* กำหนดสไตล์สำหรับเซลล์ในตาราง */
        td {
            padding: 5px; /* ระยะห่างภายใน */
            border: 0.5px solid #000; /* เส้นขอบ */
            font-size: 14pt; /* ขนาดตัวอักษร */
        }
        /* กำหนดสไตล์สำหรับการจัดกึ่งกลาง */
        .text-center { text-align: center; }
        /* กำหนดสไตล์สำหรับการจัดชิดขวา */
        .text-end { text-align: right; }
        /* กำหนดสไตล์สำหรับ badge */
        .badge {
            background-color: #0dcaf0; /* สีพื้นหลัง */
            color: #000; /* สีตัวอักษร */
            padding: 2px 5px; /* ระยะห่างภายใน */
            border-radius: 3px; /* มุมโค้ง */
            font-size: 12pt; /* ขนาดตัวอักษร */
        }
        /* กำหนดสไตล์สำหรับตัวอักษรขนาดเล็ก */
        small {
            font-size: 10pt; /* ขนาดตัวอักษร */
        }
        /* กำหนดสไตล์สำหรับเส้นคั่น */
        hr {
            border: 0.5px solid #000; /* เส้นขอบ */
            margin: 1px 0; /* ระยะห่างด้านบนและล่าง */
        }
        /* กำหนดสไตล์สำหรับแถว */
        .row {
            clear: both; /* ล้างการลอยตัว */
            margin-bottom: 10px; /* ระยะห่างด้านล่าง */
        }
        /* กำหนดสไตล์สำหรับคอลัมน์ซ้าย */
        .col-left {
            float: left; /* ลอยตัวไปทางซ้าย */
            width: 70%; /* ความกว้าง 60% */
        }
        /* กำหนดสไตล์สำหรับคอลัมน์ขวา */
        .col-right {
            float: right; /* ลอยตัวไปทางขวา */
            width: 35%; /* ความกว้าง 35% */
            text-align: right; /* จัดชิดขวา */
        }
        /* กำหนดสไตล์สำหรับเงื่อนไข */
        .conditions {
            display: flex; /* ใช้ flexbox */
            margin: 10px 0; /* ระยะห่างด้านบนและล่าง */
        }
        .conditions div {
            display: flex; /* ใช้ flexbox */
            /* display: inline-block; แสดงผลแบบบล็อกในบรรทัดเดียวกัน */
            display: block; /* แสดงผลแบบบล็อก */
            /* width: 24%; ความกว้าง 24% */
            justify-content: space-between; /* จัดเรียงให้มีระยะห่างระหว่างกัน */
            /* vertical-align: top; จัดแนวบน */
            align-items: center; /* จัดกึ่งกลาง */
            
        }
        /* กำหนดสไตล์สำหรับลายเซ็น */
        .signatures {
            margin-top: 30px; /* ระยะห่างด้านบน */
            page-break-inside: avoid; /* หลีกเลี่ยงการแบ่งหน้า */
        }
        .signature-box {
            display: inline-block; /* แสดงผลแบบบล็อกในบรรทัดเดียวกัน */
            width: 32%; /* ความกว้าง 32% */
            text-align: center; /* จัดกึ่งกลาง */
            vertical-align: top; /* จัดแนวบน */
            min-height: 100px; /* ความสูงขั้นต่ำ */
        }
                .signature-box2 {
            display: inline-block; /* แสดงผลแบบบล็อกในบรรทัดเดียวกัน */
            width: 32%; /* ความกว้าง 32% */
            text-align: center; /* จัดกึ่งกลาง */
            vertical-align: top; /* จัดแนวบน */
            min-height: 100px; /* ความสูงขั้นต่ำ */
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="col-left">
            {{-- <h5 class="thai">{{ $quotation->seller_company }}</h5> --}}
            {{-- <h5 class="thai">{{ mb_convert_encoding($quotation->seller_company, 'UTF-8', 'auto') }}</h5> --}}
            {{-- <p class="thai" style="font-size: 16pt; font-weight: bold;">{{ $quotation->seller_company }}</p> --}}
            {{-- <p class="thai" style="font-size: 16pt; font-weight: bold;">{{ mb_convert_encoding($quotation->seller_company, 'UTF-8', 'auto') }}</p> --}}
            <p class="thai" style="font-size: 19pt;">{{ mb_convert_encoding($quotation->seller_company, 'UTF-8', 'auto') }}</p>
            {{-- <p class="thai">{{ mb_convert_encoding($quotation->seller_company, 'UTF-8', 'auto') }}</p> --}}
            <p>{{ $quotation->seller_address }}</p>
            @if($seller)
                <p>Tax ID: {{ $seller->tax_id }}</p>
            @endif
            <p>Tel: {{ $quotation->seller_phone }} | Fax: {{ $quotation->seller_fax }} | LINE: {{ $quotation->seller_line }}</p>
            <p>Email: {{ $quotation->seller_email }}</p>
        </div>
        <div class="col-right">
            <h4>Quotation</h4>
            <p><strong>No:</strong> {{ $quotation->quotation_number }}</p>
            <p><strong>Date:</strong> {{ $quotation->quotation_date }}</p>
        </div>
    </div>
    <div style="clear: both;"></div> <!-- เพิ่มการล้างการลอยตัวที่นี่ -->
    <hr>
    {{-- <br><br> --}}
    <!-- Customer Info -->
    <div class="row">
        <div class="col-left">
            Customer: {{ $quotation->customer_company }}<br>
            Contact: {{ $quotation->customer_contact_name }}<br>
            Address: {{ $quotation->customer_address }}<br>
            @if($customer)
                Tax ID: {{ $customer->taxid }}<br>
            @endif
            Tel: {{ $quotation->customer_phone }} | Fax: {{ $quotation->customer_fax }}<br>
            Email:  {{ $quotation->customer_email }}
            <p>Thank you for the opportunity to provide you a quotation for the following product(s).</p>
        </div>
        <div class="col-right text-end">
            <p>Your Ref: {{ $quotation->your_ref }}</p>
            <p>Our Ref: {{ $quotation->our_ref }}</p>
        </div>
    </div>

<div style="clear: both;"></div> <!-- เพิ่มการล้างการลอยตัวที่นี่ -->

    <!-- Items Table -->
    <table>
        <thead>
            <tr class="text-center">
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

    <!-- Sub-Total, VAT, Grand Total -->
    @php
        $vat = $quotation->total_amount * 0.07;
        $grandTotal = $quotation->total_amount + $vat;
    @endphp

    <div class="row">
        <div class="col-left">{{ $quotation->amount_in_words }}
        </div>
        <div class="col-right text-end">
            <p>Sub-Total: {{ number_format($quotation->total_amount, 2) }} THB</p>
            <p>VAT (7%): {{ number_format($vat, 2) }} THB</p>
            <h5>Grand Total: {{ number_format($grandTotal, 2) }} THB</h5>
        </div>
    </div>
    {{-- <hr> --}}
    <br><br>

    <!-- Conditions -->
    <div class="conditions">
        <div>
            Delivery: {{ $quotation->delivery }}
        </div>
        <div>
            Warranty: {{ $quotation->warranty }}
        </div>
        <div>
            Validity: {{ $quotation->validity }}
        </div>
        <div>
            Payment Terms: {{ $quotation->payment }}
        </div>
    </div>
    <hr>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            <p><strong>Customer Confirm By:</strong></p>
        </div>
        <div class="signature-box">
            <p><strong>Prepared By:</strong></p>
            <p>{{ $quotation->prepared_by }}</p>
        </div>
        <div class="signature-box">
            <p><strong>Sales Engineer:</strong></p>
            <p>{{ $quotation->sales_engineer }}</p>
        </div>
    </div>
        <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box2">
            <p>(__________________________)</p>
        </div>
        <div class="signature-box">
            <p>(__________________________)</p>
        </div>
        <div class="signature-box">
            <p>(__________________________)</p>
        </div>
    </div>
</body>
</html>