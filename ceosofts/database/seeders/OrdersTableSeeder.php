<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //>>>เพิ่มจากของเดิม
        \App\Models\Order::create([
            'customer_id' => 1, // ใช้ ID ของลูกค้าที่สร้างใน CustomersTableSeeder
            'order_number' => 'ORD001',
            'order_date' => now(),
            'total_amount' => 300.00,
            'status' => 'completed',
            'notes' => 'Sample order.',
        ]);
        //>>>เพิ่มจากของเดิม

    }
}
