<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ ค้นหา ID ของ "ไอที" และ "admin" ก่อนสร้าง User
        $itDepartment = Department::firstOrCreate(['name' => 'ไอที']);
        $adminPosition = Position::firstOrCreate(['name' => 'admin']);

        // ✅ ตรวจสอบ Role "admin" ว่ามีอยู่หรือไม่
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // ✅ ตรวจสอบว่า User มีอยู่หรือไม่ก่อนสร้างใหม่
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'department_id' => $itDepartment->id, // ✅ ใส่ค่า department_id ที่ถูกต้อง
                'position_id' => $adminPosition->id,   // ✅ ใส่ค่า position_id ที่ถูกต้อง
            ]
        );

        // ✅ **กำหนด Role "admin" ให้แน่ใจว่าไม่มี Role อื่น**
        $adminUser->syncRoles([$adminRole]); // ใช้ syncRoles() เพื่อลบ Role อื่นออกแล้วใส่ "admin" เท่านั้น
    }
}
