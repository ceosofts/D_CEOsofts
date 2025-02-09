<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                'code'           => 'P0001',
                'description'    => 'This is product A.',
                'price'          => 100.00,
                'stock_quantity' => 50,
                'sku'            => 'PRO0001',
                'is_active'      => false,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'name'           => 'Product B',
                'code'           => 'P0002',
                'description'    => 'This is product B.',
                'price'          => 200.00,
                'stock_quantity' => 30,
                'sku'            => 'PRO0002',
                'is_active'      => true,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]
        ];

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('products')->insertOrIgnore($products);
    }
}
