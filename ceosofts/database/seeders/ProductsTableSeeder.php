<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name'           => 'Product A',
                'description'    => 'This is product A.',
                'price'          => 100.00,
                'stock_quantity' => 50,
                'is_active'      => false,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'name'           => 'Product B',
                'description'    => 'This is product B.',
                'price'          => 200.00,
                'stock_quantity' => 30,
                'is_active'      => true,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData); // ✅ ใช้ Model ให้สร้าง `code` และ `sku` อัตโนมัติ
        }
    }
}
