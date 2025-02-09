<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payment_statuses')->insert([
            ['name' => 'จ่ายเงินสด'],
            ['name' => 'โอน'],
            ['name' => 'จ่ายเช็ค'],
            ['name' => 'เช็ครอเคลียลิ่ง'],
            ['name' => 'เช็คเด้ง'],
        ]);
    }
}
