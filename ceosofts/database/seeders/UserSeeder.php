<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('users')) {
            $this->command->error("Table 'users' does not exist, skipping seeder.");
            return;
        }
        
        if (!Schema::hasTable('roles')) {
            $this->command->error("Table 'roles' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('users');
            $requiredColumns = ['name', 'email', 'password', 'role'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in users table: " . implode(', ', $missingColumns));
                return;
            }
            
            // ผู้ใช้ตัวอย่าง
            $users = [
                [
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ],
                [
                    'name' => 'Manager User',
                    'email' => 'manager@example.com',
                    'password' => Hash::make('password123'),
                    'role' => 'manager',
                    'email_verified_at' => now(),
                ],
                [
                    'name' => 'Regular User',
                    'email' => 'user@example.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                ],
            ];

            $count = 0;
            foreach ($users as $userData) {
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => $userData['password'],
                        'role' => $userData['role'],
                        'email_verified_at' => $userData['email_verified_at'],
                    ]
                );
                
                // กำหนดบทบาท (role)
                if (isset($userData['role']) && !empty($userData['role'])) {
                    $role = Role::where('name', $userData['role'])->first();
                    if ($role) {
                        $user->assignRole($role);
                    }
                }
                
                $count++;
            }

            $this->command->info("Successfully seeded {$count} users");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
