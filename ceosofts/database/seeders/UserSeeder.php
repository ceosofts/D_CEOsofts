<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ ค้นหา department และ position ให้แน่ใจว่ามีอยู่
        $itDepartment = Department::firstOrCreate(['name' => 'ไอที'], ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $adminPosition = Position::firstOrCreate(['name' => 'admin'], ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        // ✅ ตรวจสอบว่า User มีอยู่หรือไม่ก่อนสร้างใหม่
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'Admin User',
                'password'          => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'remember_token'    => Str::random(10),
                'department_id'     => $itDepartment->id,
                'position_id'       => $adminPosition->id,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        );

        // ✅ ตรวจสอบ role: admin
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'], ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        
        // ✅ กำหนด role ให้แน่ใจว่ามีแค่ "admin" เท่านั้น
        $adminUser->syncRoles([$adminRole]);


        // ✅ สร้าง Manager User
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name'              => 'Manager User',
                'password'          => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'remember_token'    => Str::random(10),
                'department_id'     => 1,
                'position_id'       => 3,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        );
        $managerUser->syncRoles(['manager']);

        // ✅ สร้าง User ทั่วไป
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'              => 'Regular User',
                'password'          => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'remember_token'    => Str::random(10),
                'department_id'     => 1,
                'position_id'       => 1,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        );
        $regularUser->syncRoles(['user']);
    }
}
