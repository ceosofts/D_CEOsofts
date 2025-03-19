<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบและมี role เป็น admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }
        
        // ถ้าไม่ใช่ admin ให้ redirect กลับไปที่หน้า dashboard พร้อมแสดงข้อความ
        return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
    }
}
