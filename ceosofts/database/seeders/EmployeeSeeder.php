<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        // ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
        if (!Schema::hasTable('employees')) {
            echo "Table 'employees' does not exist!\n";
            return;
        }
        
        // ตรวจสอบว่าตารางอื่นที่เกี่ยวข้องมีอยู่จริงหรือไม่
        if (!Schema::hasTable('departments') || !Schema::hasTable('positions')) {
            echo "Related tables (departments or positions) don't exist!\n";
            return;
        }
        
        try {
            // พนักงานตัวอย่าง
            $employees = [
                [
                    'employee_code' => 'EMP001',
                    'first_name' => 'สมชาย',
                    'last_name' => 'ใจดี',
                    'email' => 'somchai@example.com',
                    'phone' => '0812345678',
                    'date_of_birth' => '1985-05-15',
                    'address' => '123 ถนนหลัก แขวงเมือง เขตกรุงเทพ',
                    'salary' => 50000,
                    'hire_date' => '2015-06-01',
                    'resignation_date' => null,
                    'employment_status' => 'active',
                    'national_id' => '1234567890123',
                    'driver_license' => 'B123456789',
                    'spouse_name' => 'สมหญิง ใจดี',
                    'tax_deductions' => 5000,
                    'emergency_contact_name' => 'สมศรี ใจดี',
                    'emergency_contact_phone' => '0898765432',
                    'department_id' => 1,
                    'position_id' => 1
                ],
                [
                    'employee_code' => 'EMP002',
                    'first_name' => 'สายลม',
                    'last_name' => 'แสงแดด',
                    'email' => 'sailom@example.com',
                    'phone' => '0823456789',
                    'date_of_birth' => '1990-07-20',
                    'address' => '456 ซอยเล็ก แขวงฝั่งธน เขตกรุงเทพ',
                    'salary' => 35000,
                    'hire_date' => '2018-03-15',
                    'resignation_date' => null,
                    'employment_status' => 'active',
                    'national_id' => '2345678901234',
                    'driver_license' => null,
                    'spouse_name' => null,
                    'tax_deductions' => 3000,
                    'emergency_contact_name' => 'น้ำฝน แสงแดด',
                    'emergency_contact_phone' => '0876543210',
                    'department_id' => 1,
                    'position_id' => 1
                ],
                [
                    'employee_code' => 'EMP003',
                    'first_name' => 'มานะ',
                    'last_name' => 'พยายาม',
                    'email' => 'mana@example.com',
                    'phone' => '0834567890',
                    'date_of_birth' => '1982-09-10',
                    'address' => '789 หมู่บ้านสุขใจ แขวงลาดพร้าว เขตกรุงเทพ',
                    'salary' => 25000,
                    'hire_date' => '2010-09-01',
                    'resignation_date' => '2022-12-31',
                    'employment_status' => 'resigned',
                    'national_id' => '3456789012345',
                    'driver_license' => 'C987654321',
                    'spouse_name' => 'มารศรี พยายาม',
                    'tax_deductions' => 6000,
                    'emergency_contact_name' => 'มานี พยายาม',
                    'emergency_contact_phone' => '0865432109',
                    'department_id' => 1,
                    'position_id' => 1
                ]
            ];

            // เพิ่มข้อมูล created_at และ updated_at อัตโนมัติ
            foreach ($employees as &$employee) {
                $employee['created_at'] = now();
                $employee['updated_at'] = now();
            }

            // ใช้ insertOrIgnore เพื่อป้องกันการใส่ข้อมูลซ้ำ
            DB::table('employees')->insertOrIgnore($employees);
            
            echo "Successfully seeded employees table\n";
        } catch (\Exception $e) {
            echo "Error seeding employees table: " . $e->getMessage() . "\n";
        }
    }
}
