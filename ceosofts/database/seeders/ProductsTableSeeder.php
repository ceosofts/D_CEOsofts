<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //>>>เพิ่มจากของเดิม
        \App\Models\Product::create([
            'name' => 'Product A',
            'code' => 'P001', // ✅ เพิ่ม `code` ที่จำเป็นต้องมี
            'description' => 'This is product A.',
            'price' => 100.00,
            'stock_quantity' => 50,
            'sku' => 'PROD001',
            'is_active' => false,
        ]);

        \App\Models\Product::create([
            'name' => 'Product B',
            'code' => 'P002', // ✅ เพิ่ม `code` ที่จำเป็นต้องมี
            'description' => 'This is product B.',
            'price' => 200.00,
            'stock_quantity' => 30,
            'sku' => 'PROD002',
            'is_active' => true,
        ]);
        //>>>เพิ่มจากของเดิม

    }
}
