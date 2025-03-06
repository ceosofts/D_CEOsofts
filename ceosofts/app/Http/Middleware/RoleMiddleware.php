<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role; // ใช้ Role ของ Spatie
use Spatie\Permission\Models\Permission; // ใช้ Permission ของ Spatie

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$rolesOrPermissions): Response
    {
        $user = Auth::user();

        /** @var \App\Models\User $user */
        if (!$user) {
            return redirect('/')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        foreach ($rolesOrPermissions as $item) {
            // ตรวจสอบว่าผู้ใช้มี Role หรือ Permission ที่ระบุหรือไม่
            if ($user->hasRole($item) || $user->hasPermissionTo($item)) {
                return $next($request);
            }
        }

        return redirect('/')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}
