<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. System & Authentication
            RolePermissionSeeder::class,
            
            // 2. Organization structure
            DepartmentSeeder::class,
            PositionSeeder::class,
            
            // 3. Master data
            PrefixSeeder::class,
            CompanySeeder::class,
            UnitSeeder::class,
            ItemStatusSeeder::class,
            
            // 4. Users & Employees
            UserSeeder::class,
            EmployeeSeeder::class,
            
            // 5. Business data
            ProductsTableSeeder::class, // อาจเปลี่ยนเป็น ProductSeeder
            CustomersTableSeeder::class, // อาจเปลี่ยนเป็น CustomerSeeder
            OrdersTableSeeder::class, // อาจเปลี่ยนเป็น OrderSeeder
            OrderItemsTableSeeder::class, // อาจเปลี่ยนเป็น OrderItemSeeder
            
            // 6. Settings
            PaymentStatusSeeder::class,
            TaxSettingSeeder::class,
            
            // 7. HR & Attendance
            CompanyHolidaySeeder::class,
            AttendanceSeeder::class,
            JobStatusSeeder::class,
        ]);
    }
}
