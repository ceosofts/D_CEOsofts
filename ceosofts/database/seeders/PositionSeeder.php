<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = ['CEO', 'Manager', 'Staff'];

        foreach ($positions as $position) {
            Position::firstOrCreate(['name' => $position]);
        }
    }
}
