<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ ดึงแผนกที่มีอยู่ในระบบ
        // $itDepartment = Department::firstOrCreate(['name' => 'ไอที']);
        // $hrDepartment = Department::firstOrCreate(['name' => 'ฝ่ายบุคคล']);

        // ✅ ดึงตำแหน่งที่มีอยู่
        // $adminPosition = Position::firstOrCreate(['name' => 'Admin']);
        // $managerPosition = Position::firstOrCreate(['name' => 'Manager']);
        // $staffPosition = Position::firstOrCreate(['name' => 'Staff']);

        // ✅ กำหนดพนักงานตัวอย่าง
        $employees = [
            [
                'first_name' => 'สมชาย',
                'last_name' => 'ใจดี',
                'email' => 'somchai@example.com',
                'national_id' => '1234567890123',
                'driver_license' => 'B123456789',
                'date_of_birth' => '1985-05-15',
                'phone' => '0812345678',
                'address' => '123 ถนนหลัก แขวงเมือง เขตกรุงเทพ',
                'emergency_contact_name' => 'สมศรี ใจดี',
                'emergency_contact_phone' => '0898765432',
                'spouse_name' => 'สมหญิง ใจดี',
                // 'children' => json_encode([
                //     ['name' => 'เด็กชาย เอ', 'age' => 10],
                //     ['name' => 'เด็กหญิง บี', 'age' => 8],
                // ]),
                'tax_deductions' => 5000.00,
                'department_id' => 1,
                'position_id' => 1,
                'salary' => 50000.00,
                'employment_status' => 'active',
                'hire_date' => '2015-06-01',
                'resignation_date' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'สายลม',
                'last_name' => 'แสงแดด',
                'email' => 'sailom@example.com',
                'national_id' => '2345678901234',
                'driver_license' => null,
                'date_of_birth' => '1990-07-20',
                'phone' => '0823456789',
                'address' => '456 ซอยเล็ก แขวงฝั่งธน เขตกรุงเทพ',
                'emergency_contact_name' => 'น้ำฝน แสงแดด',
                'emergency_contact_phone' => '0876543210',
                'spouse_name' => null,
                // 'children' => json_encode([]),
                'tax_deductions' => 3000.00,
                'department_id' => 1,
                'position_id' => 1,
                'salary' => 35000.00,
                'employment_status' => 'active',
                'hire_date' => '2018-03-15',
                'resignation_date' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'มานะ',
                'last_name' => 'พยายาม',
                'email' => 'mana@example.com',
                'national_id' => '3456789012345',
                'driver_license' => 'C987654321',
                'date_of_birth' => '1982-09-10',
                'phone' => '0834567890',
                'address' => '789 หมู่บ้านสุขใจ แขวงลาดพร้าว เขตกรุงเทพ',
                'emergency_contact_name' => 'มานี พยายาม',
                'emergency_contact_phone' => '0865432109',
                'spouse_name' => 'มารศรี พยายาม',
                // 'children' => json_encode([
                //     ['name' => 'เด็กชาย ซี', 'age' => 12],
                // ]),
                'tax_deductions' => 6000.00,
                'department_id' => 1,
                'position_id' => 1,
                'salary' => 25000.00,
                'employment_status' => 'resigned',
                'hire_date' => '2010-09-01',
                'resignation_date' => '2022-12-31',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // ✅ ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
        DB::table('employees')->insertOrIgnore($employees);
    }
}
