<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,  // 1✅ กำหนด Role และ Permission
            DepartmentSeeder::class,      // 2✅ ข้อมูลแผนก
            PositionSeeder::class,        // 3✅ ข้อมูลตำแหน่ง
            PrefixSeeder::class,          // 4✅ ข้อมูลคำนำหน้าชื่อ
            CompanySeeder::class,         // 5✅ ข้อมูลบริษัท
            UnitSeeder::class,            // 6✅ ข้อมูลหน่วยนับสินค้า
            ItemStatusSeeder::class,      // 7✅ ข้อมูลสถานะสินค้า
            UserSeeder::class,            // 8✅ สร้าง Admin User และกำหนด Role
            ProductsTableSeeder::class,   // 9✅ ข้อมูลสินค้า
            CustomersTableSeeder::class,  // 10✅ ข้อมูลลูกค้า
            OrdersTableSeeder::class,     // 11✅ ข้อมูลคำสั่งซื้อ
            OrderItemsTableSeeder::class, // 12✅ ข้อมูลรายการคำสั่งซื้อ
            PaymentStatusSeeder::class,   // 13✅ ข้อมูลสถานะการชำระเงิน
            TaxSettingSeeder::class,      // 14✅ ข้อมูลการตั้งค่าภาษี
            EmployeeSeeder::class,        // 15✅ ข้อมูลพนักงาน
            
            
        ]);
    }
}
