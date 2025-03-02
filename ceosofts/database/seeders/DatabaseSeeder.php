<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1️⃣ กำหนด Role และ Permission
            RolePermissionSeeder::class,

            // 2️⃣ ข้อมูลแผนก
            DepartmentSeeder::class,

            // 3️⃣ ข้อมูลตำแหน่ง
            PositionSeeder::class,

            // 4️⃣ ข้อมูลคำนำหน้าชื่อ
            PrefixSeeder::class,

            // 5️⃣ ข้อมูลบริษัท
            CompanySeeder::class,

            // 6️⃣ ข้อมูลหน่วยนับสินค้า
            UnitSeeder::class,

            // 7️⃣ ข้อมูลสถานะสินค้า
            ItemStatusSeeder::class,

            // 8️⃣ สร้าง Admin User และกำหนด Role
            UserSeeder::class,

            // 9️⃣ ข้อมูลสินค้า
            ProductsTableSeeder::class,

            // 🔟 ข้อมูลลูกค้า
            CustomersTableSeeder::class,

            // 11️⃣ ข้อมูลคำสั่งซื้อ
            OrdersTableSeeder::class,

            // 12️⃣ ข้อมูลรายการคำสั่งซื้อ
            OrderItemsTableSeeder::class,

            // 13️⃣ ข้อมูลสถานะการชำระเงิน
            PaymentStatusSeeder::class,

            // 14️⃣ ข้อมูลการตั้งค่าภาษี
            TaxSettingSeeder::class,

            // 15️⃣ ข้อมูลพนักงาน
            EmployeeSeeder::class,

            // 16️⃣ ข้อมูลวันหยุดของบริษัท
            CompanyHolidaySeeder::class,

            // 17️⃣ ข้อมูลการลงเวลาทำงาน
            AttendanceSeeder::class,

            // 18️⃣ (ตัวอย่าง) ข้อมูลสลิปเงินเดือน
            // PayrollSeeder::class, // ← หากคุณมี Seeder สำหรับ Payroll ให้ uncomment
        ]);
    }
}
