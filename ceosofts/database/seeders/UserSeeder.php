<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // ✅ ค้นหา ID ของแผนกและตำแหน่ง
        $itDepartment = DB::table('departments')->where('name', 'ฝ่ายไอที')->first();
        $ceoPosition = DB::table('positions')->where('name', 'CEO')->first();

        if (!$itDepartment || !$ceoPosition) {
            return; // ถ้าไม่มี department หรือ position จะไม่สร้าง User
        }

        // ✅ สร้าง User Admin ถ้ายังไม่มี
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'), // ✅ ตั้งรหัสผ่าน
                'remember_token' => Str::random(10),
                'department_id' => $itDepartment->id,
                'position_id' => $ceoPosition->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ✅ ตรวจสอบ Role & กำหนด Admin Role เท่านั้น
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->syncRoles([$adminRole]); // **ให้แน่ใจว่า User มีแค่ Role "admin"**
        }
    }
}
