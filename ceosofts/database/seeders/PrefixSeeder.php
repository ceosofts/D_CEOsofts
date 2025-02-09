<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrefixSeeder extends Seeder
{
    public function run(): void
    {
        $prefixes = [
            ['name' => 'นาย'],
            ['name' => 'นาง'],
            ['name' => 'นางสาว'],
            ['name' => 'ดร.'],
            ['name' => 'ศ.ดร.']
        ];

        // เพิ่ม timestamps ให้ข้อมูล
        $now = Carbon::now();
        $prefixes = array_map(function ($prefix) use ($now) {
            return array_merge($prefix, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $prefixes);

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('prefixes')->insertOrIgnore($prefixes);
    }
}
