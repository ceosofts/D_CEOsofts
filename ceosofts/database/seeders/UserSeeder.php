<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Department;
use App\Models\Position;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ ค้นหา department และ position ให้แน่ใจว่ามีอยู่
        $itDepartment = Department::firstOrCreate(['name' => 'ไอที']);
        $adminPosition = Position::firstOrCreate(['name' => 'admin']);

        // ✅ ตรวจสอบว่า User มีอยู่หรือไม่ก่อนสร้างใหม่
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'department_id' => $itDepartment->id, // ✅ ใช้ ID ที่แน่ใจว่ามี
                'position_id' => $adminPosition->id,  // ✅ ใช้ ID ที่แน่ใจว่ามี
            ]
        );

        // ✅ ตรวจสอบ role: admin
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        // ✅ กำหนด role ให้แน่ใจว่ามีแค่ "admin" เท่านั้น
        $adminUser->syncRoles([$adminRole]);
    }
}
