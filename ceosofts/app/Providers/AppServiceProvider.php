<?php

namespace App\Providers; // ✅ ต้องมี namespace

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Post;
use App\Policies\PostPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->registerPolicies(); // 🔹 Laravel 8+ ไม่จำเป็น แต่ไม่ผิด
    }
}
