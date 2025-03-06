<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Pagination\Paginator;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // Register all the policies defined in $policies
        $this->registerPolicies();

        // ตั้งค่า pagination ให้ใช้ Bootstrap 5 สำหรับ Laravel 10+ (หากใช้ Bootstrap 4 ให้เปลี่ยนเป็น useBootstrapFour())
        Paginator::useBootstrapFive();

        // ให้ผู้ใช้ที่มี role 'super_admin' มีสิทธิ์ทุกอย่าง
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
            return null;
        });
    }
}
