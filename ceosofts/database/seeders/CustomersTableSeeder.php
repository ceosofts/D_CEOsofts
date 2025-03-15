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
        // สร้างรหัสลูกค้าใหม่ในรูปแบบ "CUSxxxx"
        // ตัวอย่าง code เดิม: "CUS0001"
        $lastCode = DB::table('customers')->max('code'); // ดึง code สูงสุด
        $lastNumber = $lastCode ? intval(substr($lastCode, 3)) : 0;
        $newCode = 'CUS' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        $customers = [
            [
                'companyname'   => 'Acme Corporation',
                'contact_name'  => 'John Doe',
                'email'         => 'john@example.com',
                'phone'         => '123456789',
                'address'       => '123 Main Street',
                'taxid'         => '1234567890123',
                'branch'        => 'Head Office', // เพิ่มข้อมูลสาขา
                'code'          => $newCode,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
            [
                'companyname'   => 'Beta Industries',
                'contact_name'  => 'Jane Smith',
                'email'         => 'jane@example.com',
                'phone'         => '987654321',
                'address'       => '456 Secondary Street',
                'taxid'         => '9876543210987',
                'branch'        => 'Branch 001', // เพิ่มข้อมูลสาขา
                'code'          => 'CUS' . str_pad($lastNumber + 2, 4, '0', STR_PAD_LEFT),
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
            // เพิ่มข้อมูลลูกค้าตัวอย่างเพิ่มเติม
            [
                'companyname'   => 'Gamma Solutions',
                'contact_name'  => 'Bob Wilson',
                'email'         => 'bob@example.com',
                'phone'         => '555666777',
                'address'       => '789 Tech Park',
                'taxid'         => '5556667770001',
                'branch'        => 'R&D Center', // เพิ่มข้อมูลสาขา
                'code'          => 'CUS' . str_pad($lastNumber + 3, 4, '0', STR_PAD_LEFT),
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
        ];

        // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('customers')->insertOrIgnore($customers);
    }
}
