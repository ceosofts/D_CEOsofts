<!-- filepath: /Users/iwasbornforthis/MyProject/D_CEOsofts/ceosofts/resources/views/invoice/invoice-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        /* กำหนดฟอนต์ THSarabunNew สำหรับการแสดงผล - ใช้แค่ตัวปกติ */
        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        /* กำหนดฟอนต์เริ่มต้นสำหรับทุกองค์ประกอบ */
        * {
            font-family: 'THSarabun', sans-serif !important;
            font-weight: normal; /* บังคับให้เป็นตัวบางเท่านั้น */
        }
        /* กำหนดสไตล์สำหรับ body */
        body {
            font-size: 13pt; /* เพิ่มขนาดตัวอักษร */
            line-height: 0.8; /* ระยะห่างระหว่างบรรทัด */
            padding: 10px; /* ระยะห่างภายใน */
        }
        /* กำหนดฟอนต์สำหรับภาษาไทย */
        .thai {
            font-family: 'THSarabun', sans-serif !important;
        }
        /* กำหนดสไตล์สำหรับหัวข้อ - เพิ่มขนาดตัวอักษรแทนการขีดเส้นใต้ */
        h4, h5 {
            font-size: 16pt; /* เพิ่มขนาดตัวอักษรแทนการใช้ตัวหนา */
            margin: 5px 0; /* ระยะห่างด้านบนและล่าง */
            font-weight: normal; /* ไม่ใช้ตัวหนา */
            /* ลบการขีดเส้นใต้ */
        }
        /* กำหนดสไตล์สำหรับย่อหน้า */
        p {
            margin: 3px 0; /* ระยะห่างด้านบนและล่าง */
        }
        /* กำหนดสไตล์สำหรับข้อความเน้น - ใช้ขนาดใหญ่กว่าแทนการขีดเส้นใต้ */
        .emphasis {
            /* ลบการขีดเส้นใต้ */
            font-size: 15pt; /* ขนาดตัวอักษรใหญ่ขึ้น */
            letter-spacing: 0.5px; /* เพิ่มระยะห่างระหว่างตัวอักษรเล็กน้อย */
            color: #000000; /* สีดำเข้มกว่าปกติ */
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
            font-size: 14pt; /* ขนาดตัวอักษร */
            padding: 5px; /* ระยะห่างภายใน */
            border: 0.5px solid #000; /* เส้นขอบ */
            text-align: center; /* ตัวอักษรกลาง */
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
        /* กำหนดสไตล์สำหรับ badge - เปลี่ยนเป็นกรอบแทนพื้นหลัง */
        .badge {
            border: 1px solid #000; /* เส้นขอบ */
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
            width: 60%; /* ความกว้าง */
        }
        /* กำหนดสไตล์สำหรับคอลัมน์ขวา */
        .col-right {
            float: right; /* ลอยตัวไปทางขวา */
            width: 35%; /* ความกว้าง */
            text-align: right; /* จัดชิดขวา */
        }
        /* กำหนดสไตล์สำหรับเงื่อนไข */
        .conditions {
            margin: 10px 0; /* ระยะห่างด้านบนและล่าง */
        }
        .conditions div {
            display: block; /* แสดงผลแบบบล็อก */
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
            min-height: 50px; /* ความสูงขั้นต่ำ */
        }
        /* สไตล์สำหรับกรอบสถานะการชำระเงิน */
        .payment-status {
            border: 1px solid #000;
            padding: 5px;
            display: inline-block;
            margin-top: 5px;
        }
        /* กำหนดสไตล์สำหรับชื่อบริษัท */
        .company-name {
            font-size: 19pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="col-left">
            <p class="thai company-name">{{ $invoice->quotation->seller_company }}</p>
            <p>{{ $invoice->quotation->seller_address }}</p>
            <p>Tax ID: {{ $invoice->quotation->seller_tax_id ?? '-' }} | Branch: {{ $invoice->quotation->seller_branch ?? 'สำนักงานใหญ่' }}</p>
            <p>Tel: {{ $invoice->quotation->seller_phone }} | Fax: {{ $invoice->quotation->seller_fax }}</p>
            <p>Email: {{ $invoice->quotation->seller_email }}</p>
        </div>
        <div class="col-right">
            <h4>ใบแจ้งหนี้ / INVOICE</h4>
            <p><span class="emphasis">เลขที่:</span> {{ $invoice->invoice_number }}</p>
            <p><span class="emphasis">วันที่:</span> {{ is_object($invoice->invoice_date) ? $invoice->invoice_date->format('d/m/Y') : $invoice->invoice_date }}</p>
            <p><span class="emphasis">ครบกำหนด:</span> {{ is_object($invoice->due_date) ? $invoice->due_date->format('d/m/Y') : $invoice->due_date }}</p>
            {{-- @if($invoice->status)
                <div class="payment-status">{{ $invoice->status->name }}</div>
            @endif --}}
        </div>
    </div>
    <div style="clear: both;"></div>
    <hr>
    
    <!-- Customer Info -->
    <div class="row">
        <div class="col-left">
            <p><span class="emphasis">ลูกค้า:</span> {{ $invoice->quotation->customer_company }}</p>
            <p><span class="emphasis">ผู้ติดต่อ:</span> {{ $invoice->quotation->customer_contact_name }}</p>
            <p><span class="emphasis">ที่อยู่:</span> {{ $invoice->quotation->customer_address }}</p>
            <p><span class="emphasis">โทรศัพท์:</span> {{ $invoice->quotation->customer_phone }} | <span class="emphasis">แฟกซ์:</span> {{ $invoice->quotation->customer_fax }}</p>
            <p><span class="emphasis">อีเมล:</span> {{ $invoice->quotation->customer_email }}</p>
            <p><span class="emphasis">เงื่อนไขการชำระเงิน:</span> {{ $invoice->payment_terms }}</p>
        </div>
        <div class="col-right">
            <p><span class="emphasis">อ้างอิงของคุณ:</span> {{ $invoice->your_ref }}</p>
            <p><span class="emphasis">อ้างอิงของเรา:</span> {{ $invoice->our_ref }}</p>
            <p><span class="emphasis">เลขที่ใบเสนอราคา:</span> {{ $invoice->quotation->quotation_number }}</p>
        </div>
    </div>

    <div style="clear: both;"></div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr class="text-center">
                <th style="width:50px;">ลำดับที่</th>
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
                        <span class="emphasis">{{ $item->product->name }}</span><br>
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

    <!-- Payment Information -->
    <div class="row">
        <div class="col-left">
            <p style="font-size: 14pt;">{{ $invoice->amount_in_words }}</p>
        </div>
        <div class="col-right text-end">
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
            
            <!-- 1. ยอดรวมก่อน VAT -->
            <p><span class="emphasis">ยอดรวมก่อน VAT:</span> {{ number_format($subtotal, 2) }} THB</p>
            
            <!-- 2. VAT 7% -->
            <p><span class="emphasis">VAT 7%:</span> {{ number_format($vatAmount, 2) }} THB</p>
            
            <!-- 3. ยอดรวมสุทธิ -->
            <p style="border-bottom: 1px solid #000; padding-bottom: 5px;"><span class="emphasis">ยอดรวมสุทธิ:</span> {{ number_format($totalAmount, 2) }} THB</p>
            
            <p><span class="emphasis">เปอร์เซ็นต์การชำระ:</span> {{ number_format($paymentPercentage, 2) }}%</p>
            
            <!-- 4. จำนวนเงินที่ต้องชำระ (คำนวณจากยอดรวมสุทธิ) -->
            <p><span class="emphasis">จำนวนเงินที่ต้องชำระงวดนี้:</span> {{ number_format($paymentAmount, 2) }} THB</p>
            
            <!-- 5. ยอดคงเหลือ (คำนวณจากยอดรวมสุทธิ) -->
            <p><span class="emphasis">ยอดคงเหลือ:</span> {{ number_format($remainingBalance, 2) }} THB</p>
        </div>
    </div>
    <div style="clear: both;"></div>
    <br><br>

    <!-- Payment Details -->
    <div class="conditions">
        <div><span class="emphasis">โอนเงินได้ที่:</span> ธนาคารกสิกรไทย สาขา.................. เลขที่บัญชี XX-X-XXXXX-X</div>
        <div><span class="emphasis">ชื่อบัญชี:</span> {{ $invoice->quotation->seller_company }}</div>
    </div>
    <hr>

    <!-- Additional Information -->
    <div style="margin-top: 5px;">
        <p>หมายเหตุ:</p>
        {{-- <p>หากมีข้อสงสัยเกี่ยวกับใบแจ้งหนี้ กรุณาติดต่อแผนกบัญชีโดยเร็ว</p> --}}
    </div>
    <hr>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            <p><span class="emphasis">ลงชื่อผู้รับสินค้า:</span></p>
        </div>
        <div class="signature-box">
            <p><span class="emphasis">ลงชื่อผู้รับเงิน:</span></p>
        </div>
        <div class="signature-box">
            <p><span class="emphasis">ผู้มีอำนาจลงนาม:</span></p>
        </div>
    </div>
    <!-- Signature Lines -->
    <div class="signatures">
        <div class="signature-box">
            <p>(__________________________)</p>
            <p>วันที่ {{ is_object($invoice->invoice_date) ? $invoice->invoice_date->format('d/m/Y') : $invoice->invoice_date }}</p>
        </div>
        <div class="signature-box">
            <p>(__________________________)</p>
            <p>วันที่ {{ is_object($invoice->invoice_date) ? $invoice->invoice_date->format('d/m/Y') : $invoice->invoice_date }}</p>
        </div>
        <div class="signature-box">
            <p>(__________________________)</p>
            <p>วันที่ {{ is_object($invoice->invoice_date) ? $invoice->invoice_date->format('d/m/Y') : $invoice->invoice_date }}</p>
        </div>
    </div>
</body>
</html>