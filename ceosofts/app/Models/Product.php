<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * ตารางที่โมเดลนี้เชื่อมโยง (Table Name)
     */
    protected $table = 'products';

    /**
     * ฟิลด์ที่อนุญาตให้ทำ Mass Assignment (fillable)
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'sku',
    ];

    /**
     * ฟิลด์ที่ต้องปกป้องจาก Mass Assignment (guarded) - ใช้ถ้าไม่มี fillable
     */
    // protected $guarded = [];

    /**
     * ความสัมพันธ์กับโมเดลอื่น (ถ้ามี) เช่น Categories หรือ Orders
     */
    // ตัวอย่าง: สินค้าอาจมีหมวดหมู่
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
}
