<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\OptimizeSystem::class,
        Commands\HealthCheckCommand::class,
        Commands\CleanupCacheCommand::class,
        Commands\AddUserCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Daily maintenance tasks
        $schedule->command('system:health-check')->dailyAt('01:00')
            ->onSuccess(function () {
                Log::info('Daily health check completed successfully');
            })
            ->onFailure(function () {
                Log::error('Daily health check failed');
            });

        // Weekly optimization
        $schedule->command('system:optimize --all')->weekly()->mondays()->at('03:00')
            ->onSuccess(function () {
                Log::info('Weekly system optimization completed successfully');
            });

        // Clean cache twice a week
        $schedule->command('cache:cleanup --temp')->twiceWeekly(2, 5)->at('02:30');

        // Database maintenance
        $schedule->command('db:maintenance')->weekly()->sundays()->at('04:00');
        
        // Check for low stock items daily
        $schedule->command('inventory:check-stock --threshold=5')
            ->dailyAt('08:00')
            ->emailOutputTo(env('ADMIN_EMAIL'));
        
        // Generate daily summary report
        $schedule->command('report:daily')
            ->weekdays()
            ->at('18:00');
        
        // Clean old backups monthly
        $schedule->command('backup:clean --days=30')->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
