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
        // ✅ สร้าง Role ถี่มีแล้ว
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
            ['name' => 'leader', 'guard_name' => 'web'],
            ['name' => 'manager', 'guard_name' => 'web'],
            ['name' => 'auditor', 'guard_name' => 'web'],
            ['name' => 'super_admin', 'guard_name' => 'web'],
        ];

        DB::table('roles')->insertOrIgnore(array_map(function ($role) {
            return array_merge($role, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $roles));

        // ✅ สร้าง Permission ถี่มีแล้ว
        $permissions = [
            ['name' => 'view dashboard', 'guard_name' => 'web'], // ✅ เพิ่มเข้าไป
            ['name' => 'manage orders', 'guard_name' => 'web'],
            ['name' => 'manage users', 'guard_name' => 'web'],
            ['name' => 'manage prefixes', 'guard_name' => 'web'],
            ['name' => 'manage departments', 'guard_name' => 'web'],
            ['name' => 'manage positions', 'guard_name' => 'web'],
            ['name' => 'manage companies', 'guard_name' => 'web'],
            ['name' => 'manage units', 'guard_name' => 'web'],
            ['name' => 'manage item statuses', 'guard_name' => 'web'],
            ['name' => 'manage payment statuses', 'guard_name' => 'web'],
            ['name' => 'manage tax settings', 'guard_name' => 'web'],
            ['name' => 'audit system', 'guard_name' => 'web'],
            ['name' => 'full access', 'guard_name' => 'web'],

            ['name' => 'manage employees', 'guard_name' => 'web'],
            ['name' => 'manage attendance', 'guard_name' => 'web'],
            ['name' => 'manage holidays', 'guard_name' => 'web'],
            ['name' => 'manage salaries', 'guard_name' => 'web'],
            ['name' => 'manage deductions', 'guard_name' => 'web'],

                        // 🆕 เพิ่มสิทธิ์สำหรับ Company Holidays
            ['name' => 'view company holidays', 'guard_name' => 'web'],
            ['name' => 'create company holidays', 'guard_name' => 'web'],
            ['name' => 'edit company holidays', 'guard_name' => 'web'],
            ['name' => 'delete company holidays', 'guard_name' => 'web'],

            
        ];

        DB::table('permissions')->insertOrIgnore(array_map(function ($permission) {
            return array_merge($permission, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $permissions));

        // ✅ อัปเดต `guard_name` ถี่มีค่า NULL
        Role::whereNull('guard_name')->update(['guard_name' => 'web']);
        Permission::whereNull('guard_name')->update(['guard_name' => 'web']);

        // ✅ กำหนด Permission ให้ Role
        $rolePermissions = [
            'user' => ['view dashboard'],
            'leader' => ['view dashboard', 'manage orders'],
            'manager' => ['view dashboard', 'manage orders', 'manage users'],
            'auditor' => ['audit system'],
            'admin' => array_column($permissions, 'name'),
            'super_admin' => Permission::pluck('name')->toArray(),
        ];

                // ✅ ให้สิทธิ์กับ Role "Manager" (เฉพาะจัดการวันหยุด)
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'view company holidays',
                'create company holidays',
                'edit company holidays',
            ]);
        }

                // ✅ ให้สิทธิ์กับ Role "User" (ดูวันหยุดได้อย่างเดียว)
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->givePermissionTo([
                'view company holidays',
            ]);
        }
        

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->syncPermissions($perms);
            }
        }
    }
}
