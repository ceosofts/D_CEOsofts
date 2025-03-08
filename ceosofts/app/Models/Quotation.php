<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        // Seller
        'seller_company',
        'seller_address',
        'seller_phone',
        'seller_fax',
        'seller_line',
        'seller_email',

        // Quotation
        'quotation_number',
        'quotation_date',

        // Customer (ปรับใหม่)
        'customer_id',               // เก็บ ID ของลูกค้า (Customer)
        'customer_company',          // ชื่อบริษัทลูกค้า
        'customer_contact_name',     // ชื่อผู้ติดต่อ
        'customer_address',
        'customer_phone',
        'customer_fax',
        'customer_email',

        // Ref
        'your_ref',
        'our_ref',

        // Summary
        'total_amount',
        'amount_in_words',

        // Conditions
        'delivery',
        'warranty',
        'validity',
        'payment',

        // Signature
        'prepared_by',
        'sales_engineer'
    ];

    /**
     * ความสัมพันธ์กับรายการสินค้า (QuotationItem)
     */
    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }

    /**
     * ความสัมพันธ์กับลูกค้า (Customer) ผ่านฟิลด์ customer_id
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
