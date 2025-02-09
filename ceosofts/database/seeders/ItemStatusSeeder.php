<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'กำลังส่งของ'],
            ['name' => 'ส่งแล้ว'],
            ['name' => 'เบิกผลิต'],
            ['name' => 'ยืม'],
            ['name' => 'รอการตรวจสอบ'],
            ['name' => 'พร้อมขาย'],
            ['name' => 'หมดสต็อก']
        ];

        // เพิ่ม timestamps ให้ข้อมูล
        $now = Carbon::now();
        $statuses = array_map(function ($status) use ($now) {
            return array_merge($status, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $statuses);

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('item_statuses')->insertOrIgnore($statuses);
    }
}
