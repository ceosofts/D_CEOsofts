<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'price', 'stock_quantity', 'sku', 'barcode', 'unit_id', 'is_active'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // ✅ ถ้ายังไม่มี Code -> สร้าง Code ใหม่
            if (empty($product->code)) {
                $product->code = self::generateNewProductCode();
            }

            // ✅ ถ้ายังไม่มี SKU -> ใช้ Code + รหัสสุ่ม 4 ตัว
            if (empty($product->sku)) {
                $product->sku = strtoupper($product->code . '-' . Str::random(4));
            }

            // ✅ ถ้ายังไม่มี Barcode -> สร้างตัวเลข 12 หลัก
            if (empty($product->barcode)) {
                $product->barcode = mt_rand(100000000000, 999999999999);
            }
        });
    }

    /**
     * ฟังก์ชันสร้างรหัสสินค้าใหม่อัตโนมัติ
     */
    public static function generateNewProductCode()
    {
        $latestProduct = self::where('code', 'like', 'P%')->orderBy('id', 'desc')->first();
        $newCodeNumber = $latestProduct ? intval(substr($latestProduct->code, 1)) + 1 : 1;
        return 'P' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
