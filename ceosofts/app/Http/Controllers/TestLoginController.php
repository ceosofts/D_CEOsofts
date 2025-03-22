<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestLoginController extends Controller
{
    /**
     * แสดงข้อมูลผู้ใช้ทั้งหมดเพื่อการทดสอบ (ใช้เฉพาะใน development เท่านั้น!)
     */
    public function showUsers()
    {
        if (app()->environment('production')) {
            abort(403, 'Access denied in production environment.');
        }
        
        $users = User::all();
        
        $output = "<h1>ผู้ใช้ในระบบทั้งหมด</h1>";
        
        if ($users->isEmpty()) {
            $output .= "<div style='color: red;'>ไม่พบผู้ใช้ในระบบ กรุณาสร้างผู้ใช้ด้วยคำสั่ง php artisan db:seed --class=UserSeeder</div>";
        } else {
            $output .= "<table border='1' cellpadding='10'>";
            $output .= "<tr><th>ID</th><th>ชื่อ</th><th>Email</th><th>Role</th><th>สถานะการยืนยัน</th></tr>";
            
            foreach ($users as $user) {
                $verified = $user->email_verified_at ? 'ยืนยันแล้ว' : 'ยังไม่ยืนยัน';
                $output .= "<tr>";
                $output .= "<td>{$user->id}</td>";
                $output .= "<td>{$user->name}</td>";
                $output .= "<td>{$user->email}</td>";
                $output .= "<td>{$user->role}</td>";
                $output .= "<td>{$verified}</td>";
                $output .= "</tr>";
            }
            
            $output .= "</table>";
        }
        
        $output .= "<h2>ทดสอบการเข้าสู่ระบบ</h2>";
        $output .= "<form method='post' action='/test-login-attempt'>";
        $output .= csrf_field();
        $output .= "<div style='margin-bottom: 10px;'><input type='email' name='email' placeholder='Email' required style='padding: 5px; width: 300px;'></div>";
        $output .= "<div style='margin-bottom: 10px;'><input type='password' name='password' placeholder='Password' required style='padding: 5px; width: 300px;'></div>";
        $output .= "<button type='submit' style='padding: 5px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>ทดสอบล็อกอิน</button>";
        $output .= "</form>";
        
        $output .= "<div style='margin-top: 20px; padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6;'>";
        $output .= "<p><strong>คำแนะนำ:</strong> ถ้าคุณเพิ่งเริ่มใช้งานระบบ คุณสามารถรันคำสั่งต่อไปนี้เพื่อสร้างผู้ใช้ตัวอย่าง:</p>";
        $output .= "<code>php artisan migrate:fresh --seed</code>";
        $output .= "<p>หรือรันเฉพาะ UserSeeder:</p>"; 
        $output .= "<code>php artisan db:seed --class=UserSeeder</code>";
        $output .= "</div>";
        
        return response($output);
    }
    
    /**
     * ทดสอบการเข้าสู่ระบบด้วยข้อมูลที่ระบุ
     */
    public function testLogin(Request $request)
    {
        if (app()->environment('production')) {
            abort(403, 'Access denied in production environment.');
        }
        
        $credentials = $request->only('email', 'password');
        
        $output = "<h1>ผลการทดสอบการเข้าสู่ระบบ</h1>";
        
        // ตรวจสอบว่ามีผู้ใช้นี้หรือไม่
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            $output .= "<div style='color: red;'>ไม่พบผู้ใช้ที่มีอีเมล {$credentials['email']}</div>";
            $output .= "<a href='/test-users' style='display: inline-block; margin-top: 20px; padding: 5px 15px; background-color: #007BFF; color: white; text-decoration: none;'>กลับไปหน้าทดสอบ</a>";
            return response($output);
        }
        
        // ทดสอบตรวจสอบรหัสผ่านโดยตรง
        $passwordChecks = Hash::check($credentials['password'], $user->password);
        $output .= "<div style='margin: 20px 0;'>ตรวจสอบรหัสผ่านโดยตรง: " . ($passwordChecks ? '<span style="color:green; font-weight: bold;">ถูกต้อง ✓</span>' : '<span style="color:red; font-weight: bold;">ไม่ถูกต้อง ✗</span>') . "</div>";
        
        // ทดสอบใช้ Auth::attempt
        if (Auth::attempt($credentials)) {
            $output .= "<div style='margin: 20px 0; color:green; font-weight: bold;'>การเข้าสู่ระบบสำเร็จ! (Auth::attempt) ✓</div>";
            Auth::logout();  // ออกจากระบบทันทีเพื่อไม่ให้กระทบการใช้งานจริง
        } else {
            $output .= "<div style='margin: 20px 0; color:red; font-weight: bold;'>การเข้าสู่ระบบไม่สำเร็จ! (Auth::attempt) ✗</div>";
            
            // แสดงข้อมูลเพิ่มเติมเพื่อการตรวจสอบ
            $output .= "<div style='margin: 20px 0; padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6;'>";
            $output .= "<h3>ข้อมูลเพิ่มเติม</h3>";
            $output .= "<pre>";
            $output .= "รหัสผ่านที่ป้อน: {$credentials['password']}\n";
            $output .= "รหัสผ่านที่เข้ารหัส: {$user->password}\n";
            $output .= "</pre>";
            $output .= "</div>";
        }
        
        $output .= "<div style='margin-top: 20px;'>";
        $output .= "<a href='/test-users' style='display: inline-block; padding: 5px 15px; background-color: #007BFF; color: white; text-decoration: none;'>กลับไปหน้าทดสอบ</a>";
        $output .= "</div>";
        
        return response($output);
    }
}
