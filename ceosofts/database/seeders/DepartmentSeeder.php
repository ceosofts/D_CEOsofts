<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'ฝ่ายขาย'],
            ['name' => 'ฝ่ายการตลาด'],
            ['name' => 'ฝ่ายบัญชี'],
            ['name' => 'ฝ่ายไอที'],
            ['name' => 'ฝ่ายบุคคล'],
            ['name' => 'ฝ่ายบริหาร'],
            ['name' => 'ฝ่ายวิจัยและพัฒนา'],
            ['name' => 'ฝ่ายการผลิต'],
            ['name' => 'ฝ่ายฝึกอบรม'],
            ['name' => 'ฝ่ายความปลอดภัย'],
            ['name' => 'ฝ่ายบริการลูกค้า'],
            ['name' => 'ฝ่ายธุรการ'],
            ['name' => 'ฝ่ายขนส่ง'],
            ['name' => 'ฝ่ายสื่อสาร'],
            ['name' => 'ฝ่ายศูนย์ข้อมูล']
        ];

        // เพิ่ม timestamps ให้ข้อมูล
        $now = Carbon::now();
        $departments = array_map(function ($department) use ($now) {
            return array_merge($department, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $departments);

        // ใช้ insertOrIgnore() เพื่อป้องกันการเพิ่มข้อมูลซ้ำ
        DB::table('departments')->insertOrIgnore($departments);
    }
}
