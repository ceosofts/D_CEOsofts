<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // System maintenance tasks
        $schedule->command('inspire')->hourly();
        $schedule->command('db:optimize')->daily()->at('01:00');
        
        // Data backup tasks
        $schedule->command('backup:clean')->daily()->at('01:30');
        $schedule->command('backup:run')->daily()->at('02:00');
        
        // Cache management
        $schedule->command('cache:prune-stale-tags')->hourly();
        
        // Custom application tasks
        // Add your custom scheduled tasks here
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
