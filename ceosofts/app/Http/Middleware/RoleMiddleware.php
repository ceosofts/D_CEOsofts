<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        // ✅ ถ้า User ไม่มี Role หรือ Role ไม่ตรงกับที่กำหนด → Redirect ออกจากหน้านี้
        if (!$user || !in_array($user->role, $roles)) {
            return redirect('/')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
