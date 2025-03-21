<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\ColorHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add isDarkColor helper as a Blade directive
        Blade::directive('isDarkColor', function ($expression) {
            return "<?php echo App\Helpers\ColorHelper::isDarkColor($expression) ? 'white' : 'black'; ?>";
        });

        // เพิ่มฟังก์ชัน isDarkColor ให้ Blade
        Blade::if('isDark', function ($color) {
            return ColorHelper::isDarkColor($color);
        });

        // เพิ่ม logging สำหรับ database queries ในโหมด debug
        if (config('app.debug')) {
            DB::listen(function (QueryExecuted $query) {
                // Log เฉพาะ query ที่ใช้เวลานานเกิน 500ms
                if ($query->time > 500) {
                    Log::channel('queries')->info(
                        'SLOW QUERY: ' . $query->sql,
                        [
                            'time' => $query->time . 'ms',
                            'bindings' => $query->bindings,
                            'connection' => $query->connection->getName(),
                        ]
                    );
                }
            });
        }
        
        // ตั้งค่าค่าเริ่มต้นสำหรับการเชื่อมต่อฐานข้อมูล
        DB::statement('SET SESSION sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"');
    }
}
