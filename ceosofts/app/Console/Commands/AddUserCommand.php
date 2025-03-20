<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:user 
                            {name : ชื่อของผู้ใช้} 
                            {email : อีเมลของผู้ใช้}
                            {--r|role= : บทบาทของผู้ใช้ (admin, manager, employee, customer)}
                            {--p|password= : รหัสผ่าน (จะถูกสร้างอัตโนมัติถ้าไม่ได้ระบุ)}
                            {--f|force : ข้ามการยืนยัน}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'เพิ่มผู้ใช้ใหม่อย่างรวดเร็ว';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $role = $this->option('role') ?: 'employee';
        $password = $this->option('password') ?: Str::random(10);
        $force = $this->option('force');

        // Validate email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $this->error('รูปแบบอีเมลไม่ถูกต้อง');
            return 1;
        }

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error("อีเมล '{$email}' มีอยู่ในระบบแล้ว");
            return 1;
        }

        // Display confirmation
        if (!$force) {
            $this->info('⚠️ กำลังจะเพิ่มผู้ใช้งานใหม่:');
            $this->table(['ข้อมูล', 'ค่า'], [
                ['ชื่อ', $name],
                ['อีเมล', $email],
                ['บทบาท', $role],
                ['รหัสผ่าน', $this->option('password') ? $password : $password . ' (สร้างอัตโนมัติ)']
            ]);

            if (!$this->confirm('ยืนยันการเพิ่มผู้ใช้งานนี้?')) {
                $this->info('⚪ ยกเลิกการดำเนินการ');
                return 0;
            }
        }

        $this->info('🔄 กำลังเพิ่มผู้ใช้งานใหม่...');

        try {
            // Create user
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->email_verified_at = now();
            $user->password = Hash::make($password);
            $user->remember_token = Str::random(10);
            $user->save();

            // Assign role if it exists
            if (Role::where('name', $role)->exists()) {
                $user->assignRole($role);
                $this->line("✅ กำหนดบทบาท '{$role}' ให้กับผู้ใช้งานเรียบร้อยแล้ว");
            } else {
                $this->warn("⚠️ ไม่พบบทบาท '{$role}' ในระบบ กรุณากำหนดบทบาทให้ผู้ใช้ภายหลัง");
            }

            $this->info("✅ เพิ่มผู้ใช้งาน '{$name}' เรียบร้อยแล้ว");
            $this->info("🔑 รหัสผ่าน: {$password}");
            
            // Show command to reset password if needed
            $this->comment("หากต้องการรีเซ็ตรหัสผ่าน สามารถใช้คำสั่ง:");
            $this->line("php artisan password:reset {$email}");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ เกิดข้อผิดพลาด: {$e->getMessage()}");
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}
