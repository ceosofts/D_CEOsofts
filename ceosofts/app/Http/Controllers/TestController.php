<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class TestController extends Controller
{
    /**
     * แสดง route ที่ลงทะเบียนทั้งหมด
     */
    public function index()
    {
        if (!app()->environment('local')) {
            return "Test controller only available in local environment.";
        }

        echo "<h1>All Registered Routes</h1>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th></tr>";
        
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            echo "<tr>";
            echo "<td>" . implode('|', $route->methods()) . "</td>";
            echo "<td>" . $route->uri() . "</td>";
            echo "<td>" . $route->getName() . "</td>";
            echo "<td>" . $route->getActionName() . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        echo "<h2>Environment Variables</h2>";
        echo "<pre>";
        echo "APP_ENV: " . env('APP_ENV') . "\n";
        echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
        echo "</pre>";
        
        echo "<h2>Test Links</h2>";
        echo "<ul>";
        echo "<li><a href='/test-users'>Test Users Page</a></li>";
        echo "</ul>";
    }
}
