<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Pagination\Paginator;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // ✅ ใช้ Bootstrap Pagination
        Paginator::useBootstrapFive(); // ใช้ Bootstrap 5 (Laravel 10+)
        // Paginator::useBootstrapFour(); // ถ้าต้องการใช้ Bootstrap 4

        // ✅ ให้ Super Admin มีสิทธิ์ทุกอย่าง
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
