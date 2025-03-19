<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuppressDeprecationWarnings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Temporarily suppress deprecation warnings during request handling
        $errorReporting = error_reporting();
        error_reporting($errorReporting & ~E_DEPRECATED);
        
        $response = $next($request);
        
        // Restore original error reporting level after request
        error_reporting($errorReporting);
        
        return $response;
    }
}
