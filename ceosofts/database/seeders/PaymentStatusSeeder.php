<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'จ่ายเงินสด'],
            ['name' => 'โอน'],
            ['name' => 'จ่ายเช็ค'],
            ['name' => 'เช็ครอเคลียลิ่ง'],
            ['name' => 'เช็คเด้ง']
        ];

        // เพิ่ม timestamp ให้กับข้อมูล
        $now = Carbon::now();
        $statuses = array_map(function ($status) use ($now) {
            return array_merge($status, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $statuses);

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('payment_statuses')->insertOrIgnore($statuses);
    }
}
