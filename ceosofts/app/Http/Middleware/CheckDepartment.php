<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ใช้ Auth Facade แทนการใช้ auth() helper
        $user = Auth::user();
        $departmentId = $request->route('department_id');

        if (!$user || !$user->department_id) {
            return redirect('/')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้!');
        }

        if ($departmentId && $user->department_id != $departmentId) {
            return redirect('/dashboard')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงแผนกนี้!');
        }

        return $next($request);
    }
}
