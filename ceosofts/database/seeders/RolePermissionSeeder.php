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
        // ✅ สร้าง Role (ถ้ายังไม่มี)
        $roles = ['admin', 'user', 'leader', 'manager', 'auditor', 'super_admin'];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role, 'guard_name' => 'web'],
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );
        }

        // ✅ สร้าง Permission (ถ้ายังไม่มี)
        $permissions = [
            'view dashboard', 'manage orders', 'manage users', 'manage prefixes',
            'manage departments', 'manage positions', 'manage companies', 'manage units',
            'manage item statuses', 'manage payment statuses', 'manage tax settings',
            'audit system', 'full access', 'manage employees', 'manage attendance',
            'manage holidays', 'manage salaries', 'manage deductions',
            'view company holidays', 'create company holidays', 'edit company holidays', 'delete company holidays',
            'view attendance', 'create attendance', 'edit attendance', 'delete attendance', 'Wage', 'WorkShift'
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );
        }

        // ✅ ให้ Role มีสิทธิ์ที่กำหนด
        $rolePermissions = [
            'user' => ['view dashboard', 'view company holidays'],
            'leader' => ['view dashboard', 'manage orders'],
            'manager' => ['view dashboard', 'manage orders', 'manage users', 'view company holidays', 'create company holidays', 'edit company holidays'],
            'auditor' => ['audit system'],
            'admin' => Permission::pluck('name')->toArray(),
            'super_admin' => Permission::pluck('name')->toArray(),
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->syncPermissions($perms);
            }
        }
    }
}
