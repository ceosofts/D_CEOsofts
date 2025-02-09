<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'customer_id'  => 1, // ใช้ ID ของลูกค้าที่สร้างใน CustomersTableSeeder
                'order_number' => 'ORD001',
                'order_date'   => Carbon::now(),
                'total_amount' => 300.00,
                'status'       => 'completed',
                'notes'        => 'Sample order.',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]
        ];

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('orders')->insertOrIgnore($orders);
    }
}
