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

        try {
            // ลบ record เก่า - เพื่อป้องกันปัญหาการมี record ซ้ำ
            // DB::table('users')->truncate(); // ถ้ามี foreign keys ให้ comment บรรทัดนี้ไว้
            
            // ผู้ใช้ตัวอย่าง - กำหนดรหัสผ่านชัดเจนเป็น 'password' สำหรับการทดสอบ
            $users = [
                [
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('password'), // ตรวจสอบว่ารหัสผ่านเป็น 'password'
                    'role' => 'admin',
                ],
                [
                    'name' => 'Manager User',
                    'email' => 'manager@example.com',
                    'password' => 'password', // จะถูก hash ในภายหลัง
                    'role' => 'manager',
                ],
                [
                    'name' => 'Regular User',
                    'email' => 'user@example.com',
                    'password' => 'password', // จะถูก hash ในภายหลัง
                    'role' => 'user',
                ],
            ];

            $count = 0;
            foreach ($users as $userData) {
                // Hash รหัสผ่านให้ถูกต้อง
                $hashedPassword = Hash::make($userData['password']);
                
                // ลบ record เก่าเฉพาะ email นั้น (เพื่อป้องกันการซ้ำซ้อน)
                User::where('email', $userData['email'])->delete();
                
                // สร้าง user ใหม่
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $hashedPassword,
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                ]);
                
                // กำหนดบทบาท (role)
                if (isset($userData['role']) && !empty($userData['role'])) {
                    // ตรวจสอบว่ามี role นี้แล้วหรือไม่
                    $role = Role::where('name', $userData['role'])->first();
                    if ($role) {
                        $user->assignRole($role);
                    } else {
                        $this->command->info("Role '{$userData['role']}' not found, creating it.");
                        // สร้าง role ใหม่ถ้ายังไม่มี
                        $role = Role::create(['name' => $userData['role']]);
                        $user->assignRole($role);
                    }
                }
                
                $count++;
            }

            $this->command->info("Successfully seeded {$count} users with password 'password'");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
