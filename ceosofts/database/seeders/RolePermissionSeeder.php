<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ สร้าง Role ถ้ายังไม่มี
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // ✅ กำหนดสิทธิ์ที่สำคัญ
        $permissions = [
            'manage users',
            'manage prefixes',
            'manage departments',
            'manage positions',
            'manage companies',
            'manage units',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ✅ ให้ Role Admin มีสิทธิ์ทั้งหมด
        $adminRole->syncPermissions($permissions);
    }
}
