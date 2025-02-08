<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = ['พนักงาน', 'หัวหน้างาน', 'หัวหน้าแผนก', 'หัวหน้าฝ่าย', 'ผู้จัดการ', 'admin'];

        foreach ($positions as $position) {
            Position::firstOrCreate(['name' => $position]);
        }
    }
}
