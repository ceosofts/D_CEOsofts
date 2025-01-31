<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ เพิ่ม Middleware "role"
        $middleware->alias([
            'department' => \App\Http\Middleware\CheckDepartment::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class, // ✅ เพิ่ม Middleware Role
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
