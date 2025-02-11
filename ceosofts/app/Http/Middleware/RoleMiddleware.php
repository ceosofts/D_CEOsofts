<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role; // ✅ ใช้ Role ของ Spatie
use Spatie\Permission\Models\Permission; // ✅ ใช้ Permission ของ Spatie

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$rolesOrPermissions): Response
    {
        $user = auth()->user();

        // ✅ ถ้า User ไม่มี Role หรือ Permission ที่ตรงกับที่กำหนด → Redirect ออกจากหน้านี้
        if (!$user) {
            return redirect('/')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // ✅ ตรวจสอบว่า User มี Role หรือ Permission ที่กำหนดหรือไม่
        foreach ($rolesOrPermissions as $item) {
            if ($user->hasRole($item) || $user->hasPermissionTo($item)) {
                return $next($request);
            }
        }

        return redirect('/')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}
