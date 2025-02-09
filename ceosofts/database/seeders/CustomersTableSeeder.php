<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '123456789',
                'address' => '123 Main Street',
                'taxid' => '1234567890123',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('customers')->insertOrIgnore($customers);
    }
}
