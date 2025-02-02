<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'price', 'stock_quantity', 'sku', 'unit_id'];

    protected $guarded = []; // ✅ ป้องกันข้อผิดพลาดจาก Mass Assignment

    public $timestamps = true; // ✅ เปิดใช้งาน timestamps (ถ้ามีใน DB)

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id'); // ✅ เชื่อมกับ Unit ผ่าน unit_id
    }
}
