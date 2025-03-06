<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'stock_quantity',
        'sku',
        'barcode',
        'unit_id',
        'is_active',
    ];

    /**
     * Boot the model.
     *
     * This method automatically sets the product code, SKU, and barcode when creating a new product.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Generate product code if not provided.
            if (empty($product->code)) {
                $product->code = self::generateNewProductCode();
            }

            // Generate SKU if not provided (using product code + random 4-character string).
            if (empty($product->sku)) {
                $product->sku = strtoupper($product->code . '-' . Str::random(4));
            }

            // Generate Barcode if not provided (12-digit random number as a string).
            if (empty($product->barcode)) {
                $product->barcode = (string) mt_rand(100000000000, 999999999999);
            }
        });
    }

    /**
     * Generate a new product code automatically.
     *
     * @return string
     */
    public static function generateNewProductCode(): string
    {
        $latestProduct = self::where('code', 'like', 'P%')
            ->orderBy('id', 'desc')
            ->first();

        $newCodeNumber = $latestProduct ? intval(substr($latestProduct->code, 1)) + 1 : 1;
        return 'P' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Define a relationship to the Unit model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
