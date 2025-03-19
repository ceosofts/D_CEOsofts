<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง roles และ permissions
        if (!Schema::hasTable('roles')) {
            $this->command->error("Table 'roles' does not exist, skipping seeder.");
            return;
        }
        
        if (!Schema::hasTable('permissions')) {
            $this->command->error("Table 'permissions' does not exist, skipping seeder.");
            return;
        }
        
        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $roleColumns = Schema::getColumnListing('roles');
            $permissionColumns = Schema::getColumnListing('permissions');
            
            $requiredRoleColumns = ['name', 'guard_name'];
            $requiredPermissionColumns = ['name', 'guard_name'];
            
            $missingRoleColumns = array_diff($requiredRoleColumns, $roleColumns);
            $missingPermissionColumns = array_diff($requiredPermissionColumns, $permissionColumns);
            
            if (!empty($missingRoleColumns)) {
                $this->command->error("Missing columns in roles table: " . implode(', ', $missingRoleColumns));
                return;
            }
            
            if (!empty($missingPermissionColumns)) {
                $this->command->error("Missing columns in permissions table: " . implode(', ', $missingPermissionColumns));
                return;
            }

            // สร้าง roles
            $roles = [
                'admin', 'manager', 'user', 'leader', 'auditor', 'super_admin'
            ];

            foreach ($roles as $role) {
                Role::updateOrCreate(['name' => $role], [
                    'name' => $role,
                    'guard_name' => 'web',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // สร้าง permissions
            $permissions = [
                'user_management_access',
                'user_management_create',
                'user_management_edit',
                'user_management_view',
                'user_management_delete',
                'role_access',
                'role_create',
                'role_edit',
                'role_view',
                'role_delete',
                'permission_access',
                'permission_create',
                'permission_edit',
                'permission_view',
                'permission_delete',
                'product_access',
                'product_create',
                'product_edit',
                'product_view',
                'product_delete',
                'customer_access',
                'customer_create',
                'customer_edit',
                'customer_view', 
                'customer_delete',
                'order_access',
                'order_create',
                'order_edit',
                'order_view',
                'order_delete',
                'company_access',
                'company_create',
                'company_edit',
                'company_view',
                'company_delete',
                'view dashboard', 'manage orders', 'manage users', 'manage prefixes',
                'manage departments', 'manage positions', 'manage companies', 'manage units',
                'manage item statuses', 'manage payment statuses', 'manage tax settings',
                'audit system', 'full access', 'manage employees', 'manage attendance',
                'manage holidays', 'manage salaries', 'manage deductions',
                'view company holidays', 'create company holidays', 'edit company holidays', 'delete company holidays',
                'view attendance', 'create attendance', 'edit attendance', 'delete attendance', 'Wage', 'WorkShift',
                'manage job statuses',
                'view invoices',
                'create invoice',
                'edit invoice',
                'delete invoice',
                'view quotations',
                'create quotation',
                'edit quotation',
                'delete quotation'
            ];

            foreach ($permissions as $permission) {
                Permission::updateOrCreate(['name' => $permission], [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // ให้สิทธิ์ admin สามารถทำทุกอย่างได้
            $adminRole = Role::where('name', 'admin')->first();
            $adminRole->givePermissionTo(Permission::all());

            // ให้สิทธิ์ manager
            $managerRole = Role::where('name', 'manager')->first();
            $managerPermissions = [
                'product_access', 'product_view', 
                'customer_access', 'customer_view',
                'order_access', 'order_view', 'order_create', 'order_edit',
                'company_access', 'company_view'
            ];
            $managerRole->givePermissionTo($managerPermissions);

            // ให้สิทธิ์ user
            $userRole = Role::where('name', 'user')->first();
            $userPermissions = [
                'product_view', 
                'customer_view',
                'order_view',
                'company_view'
            ];
            $userRole->givePermissionTo($userPermissions);

            // ให้สิทธิ์ leader, auditor, super_admin
            $rolePermissions = [
                'leader' => ['view dashboard', 'manage orders'],
                'auditor' => ['audit system'],
                'super_admin' => Permission::pluck('name')->toArray(),
            ];

            foreach ($rolePermissions as $roleName => $perms) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $role->syncPermissions($perms);
                }
            }

            $this->command->info("Roles, permissions and relationships created successfully");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
