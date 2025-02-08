<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prefix;

class PrefixSeeder extends Seeder
{
    public function run(): void
    {
        $prefixes = ['นาย', 'นาง', 'นางสาว', 'ดร.', 'ศ.ดร.'];

        foreach ($prefixes as $prefix) {
            Prefix::firstOrCreate(['name' => $prefix]);
        }
    }
}
