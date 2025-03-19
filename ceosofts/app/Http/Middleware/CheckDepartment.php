<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $department
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $department = null): Response
    {
        if (!$request->user() || ($department && $request->user()->department !== $department)) {
            return response()->json(['message' => 'Unauthorized. Insufficient department permissions.'], 403);
        }

        return $next($request);
    }
}
