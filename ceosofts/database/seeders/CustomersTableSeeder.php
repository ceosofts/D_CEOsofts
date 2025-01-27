<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\Customer::create([  //>>>เพิ่มจากของเดิม
            'name' => 'John Doe', //>>>เพิ่มจากของเดิม
            'email' => 'john@example.com', //>>>เพิ่มจากของเดิม
            'phone' => '123456789', //>>>เพิ่มจากของเดิม
            'address' => '123 Main Street', //>>>เพิ่มจากของเดิม
            'taxid' => '1234567890123', //>>>เพิ่มจากของเดิม
        ]);  //>>>เพิ่มจากของเดิม

    }
}
