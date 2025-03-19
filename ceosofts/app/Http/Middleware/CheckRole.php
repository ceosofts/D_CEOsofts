<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        if (!$request->user() || ($role && $request->user()->role !== $role)) {
            return response()->json(['message' => 'Unauthorized. Insufficient role permissions.'], 403);
        }

        return $next($request);
    }
}
