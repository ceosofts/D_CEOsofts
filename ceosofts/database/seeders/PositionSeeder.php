<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['name' => 'พนักงาน'],
            ['name' => 'หัวหน้างาน'],
            ['name' => 'หัวหน้าแผนก'],
            ['name' => 'หัวหน้าฝ่าย'],
            ['name' => 'ผู้จัดการ'],
            ['name' => 'admin']
        ];

        // เพิ่ม timestamps ให้ข้อมูล
        $now = Carbon::now();
        $positions = array_map(function ($position) use ($now) {
            return array_merge($position, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $positions);

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('positions')->insertOrIgnore($positions);
    }
}
