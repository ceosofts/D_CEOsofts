<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderItems = [
            [
                'order_id'   => 1, // ใช้ ID ของคำสั่งซื้อที่สร้างใน OrdersTableSeeder
                'product_id' => 1, // ใช้ ID ของสินค้าที่สร้างใน ProductsTableSeeder
                'quantity'   => 2,
                'unit_price' => 100.00,
                'subtotal'   => 200.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'order_id'   => 1,
                'product_id' => 2,
                'quantity'   => 1,
                'unit_price' => 200.00,
                'subtotal'   => 200.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('order_items')->insertOrIgnore($orderItems);
    }
}
