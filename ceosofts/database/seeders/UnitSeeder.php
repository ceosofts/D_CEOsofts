<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'ชิ้น'],
            ['name' => 'กล่อง'],
            ['name' => 'แพ็ค'],
            ['name' => 'ลิตร'],
            ['name' => 'กิโลกรัม']
        ];

        // เพิ่ม timestamps ให้ข้อมูล
        $now = Carbon::now();
        $units = array_map(function ($unit) use ($now) {
            return array_merge($unit, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $units);

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('units')->insertOrIgnore($units);
    }
}
