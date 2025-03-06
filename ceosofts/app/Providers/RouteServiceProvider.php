<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * เส้นทางของหน้า "home" สำหรับแอปพลิเคชัน
     * ซึ่งจะใช้สำหรับการ redirect หลังจากการเข้าสู่ระบบ
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * กำหนดการ binding ของ route model และ pattern filters ต่าง ๆ
     */
    public function boot(): void
    {
        // สามารถเพิ่ม route model bindings หรือ pattern filters ที่นี่ได้

        parent::boot();
    }

    /**
     * กำหนดเส้นทางทั้งหมดสำหรับแอปพลิเคชัน
     */
    public function map(): void
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * กำหนดเส้นทางเว็บ (Web Routes)
     * เส้นทางเหล่านี้จะใช้ middleware 'web' เพื่อให้มี session, CSRF protection เป็นต้น
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    /**
     * กำหนดเส้นทาง API (API Routes)
     * เส้นทางเหล่านี้จะใช้ middleware 'api' ซึ่งโดยปกติแล้วจะเป็นแบบ stateless
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }
}
