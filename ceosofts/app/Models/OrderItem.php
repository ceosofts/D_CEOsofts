<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * กำหนดฟิลด์ที่สามารถทำ Mass Assignment ได้
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price' // เพิ่ม field นี้หากมีการบันทึกราคาในแต่ละรายการ
    ];

    /**
     * ความสัมพันธ์: OrderItem เป็นของ Order หนึ่งใบ
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * ความสัมพันธ์: OrderItem เกี่ยวข้องกับ Product หนึ่งรายการ
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
