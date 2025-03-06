<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * กำหนดฟิลด์ที่สามารถทำ Mass Assignment ได้
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'order_date',
        'status',
        'total_amount',
        'remarks'
    ];

    /**
     * ความสัมพันธ์: Order มีหลาย OrderItems
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * ความสัมพันธ์: Order เป็นของ Customer หนึ่งคน
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
