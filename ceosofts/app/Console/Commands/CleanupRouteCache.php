<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupRouteCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually clean up route cache files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up route cache files...');
        
        // Remove bootstrap/cache/routes-v7.php file if it exists
        $routeCacheFile = base_path('bootstrap/cache/routes-v7.php');
        if (File::exists($routeCacheFile)) {
            File::delete($routeCacheFile);
            $this->info("Deleted route cache file: {$routeCacheFile}");
        } else {
            $this->info("Route cache file not found at: {$routeCacheFile}");
        }
        
        // Alternative location for older Laravel versions
        $altRouteCacheFile = base_path('bootstrap/cache/routes.php');
        if (File::exists($altRouteCacheFile)) {
            File::delete($altRouteCacheFile);
            $this->info("Deleted alternative route cache file: {$altRouteCacheFile}");
        }
        
        // Clean up the compiled services file too
        $servicesFile = base_path('bootstrap/cache/services.php');
        if (File::exists($servicesFile)) {
            File::delete($servicesFile);
            $this->info("Deleted compiled services file: {$servicesFile}");
        }
        
        $this->info('Route cache cleanup completed!');
        
        // Add some helpful information on next steps
        $this->info('');
        $this->info('You should now:');
        $this->info('1. Run "php artisan app:fix-permissions" to fix directory permissions');
        $this->info('2. Run "php artisan config:clear" to clear the configuration cache');
        $this->info('3. Run "php artisan cache:clear" to clear the application cache');
        
        return Command::SUCCESS;
    }
}
