<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ สร้าง Role ถ้ายังไม่มี
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
        ];

        // ใช้ insertOrIgnore() เพื่อป้องกัน Role ซ้ำ
        DB::table('roles')->insertOrIgnore(array_map(function ($role) {
            return array_merge($role, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $roles));

        // ✅ สร้าง Permission ถ้ายังไม่มี
        $permissions = [
            ['name' => 'manage users', 'guard_name' => 'web'],
            ['name' => 'manage prefixes', 'guard_name' => 'web'],
            ['name' => 'manage departments', 'guard_name' => 'web'],
            ['name' => 'manage positions', 'guard_name' => 'web'],
            ['name' => 'manage companies', 'guard_name' => 'web'],
            ['name' => 'manage units', 'guard_name' => 'web'],
            ['name' => 'manage item statuses', 'guard_name' => 'web'],
            ['name' => 'manage payment statuses', 'guard_name' => 'web'],
        ];

        // ใช้ insertOrIgnore() เพื่อป้องกัน Permission ซ้ำ
        DB::table('permissions')->insertOrIgnore(array_map(function ($permission) {
            return array_merge($permission, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $permissions));

        // ✅ อัปเดต `guard_name` ถ้ามีค่า NULL
        Role::whereNull('guard_name')->update(['guard_name' => 'web']);
        Permission::whereNull('guard_name')->update(['guard_name' => 'web']);

        // ✅ กำหนด Permission ให้ Role Admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(array_column($permissions, 'name'));
        }
    }
}
