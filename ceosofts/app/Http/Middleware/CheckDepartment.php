<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartment
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user(); // ✅ ดึงข้อมูลผู้ใช้ที่เข้าสู่ระบบ
        $departmentId = $request->route('department_id'); // ✅ ดึง department_id จาก URL (ถ้ามี)

        // ✅ ตรวจสอบว่าผู้ใช้มี department_id หรือไม่
        if (!$user || !$user->department_id) {
            return redirect('/')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้!');
        }

        // ✅ ตรวจสอบว่า department_id ของ User ตรงกับใน URL หรือไม่
        if ($departmentId && $user->department_id != $departmentId) {
            return redirect('/dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงแผนกนี้!');
        }

        return $next($request); // ✅ อนุญาตให้ Request ผ่านไปได้
    }
}
