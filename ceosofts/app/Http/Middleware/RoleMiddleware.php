<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        $user = Auth::user();

        // Check if user has the required role
        if ($user->hasRole($role)) {
            return $next($request);
        }

        // If the request expects JSON (API request), return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้',
                'status' => 'error'
            ], 403);
        }

        // Redirect with error message for web requests
        return redirect()
            ->back()
            ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}
