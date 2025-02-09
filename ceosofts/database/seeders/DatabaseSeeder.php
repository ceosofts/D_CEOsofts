<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,  // ✅ กำหนด Role และ Permission
            DepartmentSeeder::class,      // ✅ ข้อมูลแผนก
            PositionSeeder::class,        // ✅ ข้อมูลตำแหน่ง
            PrefixSeeder::class,          // ✅ ข้อมูลคำนำหน้าชื่อ
            CompanySeeder::class,         // ✅ ข้อมูลบริษัท
            UnitSeeder::class,            // ✅ ข้อมูลหน่วยนับสินค้า
            ItemStatusSeeder::class,      // ✅ ข้อมูลสถานะสินค้า
            UserSeeder::class,            // ✅ สร้าง Admin User และกำหนด Role
            ProductsTableSeeder::class,   // ✅ ข้อมูลสินค้า
            CustomersTableSeeder::class,  // ✅ ข้อมูลลูกค้า
            OrdersTableSeeder::class,     // ✅ ข้อมูลคำสั่งซื้อ
            OrderItemsTableSeeder::class, // ✅ ข้อมูลรายการคำสั่งซื้อ
            PaymentStatusSeeder::class,   // ✅ ข้อมูลสถานะการชำระเงิน
            
            

            
        ]);
    }
}
