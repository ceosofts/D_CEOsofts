<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ เพิ่มบรรทัดนี้

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->insert([
            'company_name' => 'บริษัท ABC จำกัด',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
