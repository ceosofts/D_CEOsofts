<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //>>>เพิ่มจากของเดิม
        \App\Models\OrderItem::create([
            'order_id' => 1, // ใช้ ID ของคำสั่งซื้อที่สร้างใน OrdersTableSeeder
            'product_id' => 1, // ใช้ ID ของสินค้าที่สร้างใน ProductsTableSeeder
            'quantity' => 2,
            'unit_price' => 100.00,
            'subtotal' => 200.00,
        ]);

        \App\Models\OrderItem::create([
            'order_id' => 1, // ใช้ ID ของคำสั่งซื้อที่สร้างใน OrdersTableSeeder
            'product_id' => 2, // ใช้ ID ของสินค้าที่สร้างใน ProductsTableSeeder
            'quantity' => 1,
            'unit_price' => 200.00,
            'subtotal' => 200.00,
        ]);
        //>>>เพิ่มจากของเดิม

    }
}
