<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = ['ชิ้น', 'กล่อง', 'แพ็ค', 'ลิตร', 'กิโลกรัม'];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit]);
        }
    }
}
