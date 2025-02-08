<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ ตรวจสอบและสร้าง Role
        $roles = ['admin', 'user'];
        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role],
                ['guard_name' => 'web'] // ✅ ถ้าไม่มีให้ใส่ค่า 'web'
            );
        }

        // ✅ ตรวจสอบและสร้าง Permission
        $permissions = [
            'manage users',
            'manage prefixes',
            'manage departments',
            'manage positions',
            'manage companies',
            'manage units',
            'manage item statuses',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web'] // ✅ ถ้าไม่มีให้ใส่ค่า 'web'
            );
        }

        // ✅ อัปเดต `guard_name` ถ้ามีค่า NULL
        Role::whereNull('guard_name')->update(['guard_name' => 'web']);
        Permission::whereNull('guard_name')->update(['guard_name' => 'web']);

        // ✅ กำหนด Permission ให้ Role Admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions($permissions);
        }
    }
}
