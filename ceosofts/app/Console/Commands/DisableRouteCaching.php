<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class DisableRouteCaching extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:disable-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Properly disable route caching by removing cache files and updating config';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Disabling route caching...');
        
        // 1. Clear route cache
        $this->call('route:clear');
        
        // 2. Remove all route cache files manually
        $cacheFiles = [
            base_path('bootstrap/cache/routes.php'),
            base_path('bootstrap/cache/routes-v7.php'),
        ];
        
        foreach ($cacheFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->info("Deleted route cache file: {$file}");
            }
        }
        
        // 3. Create an empty routes-v7.php to prevent auto-caching
        $emptyRoute = "<?php return []; // This file exists to prevent auto-caching of routes\n";
        File::put(base_path('bootstrap/cache/routes-v7.php'), $emptyRoute);
        chmod(base_path('bootstrap/cache/routes-v7.php'), 0644);
        $this->info("Created empty routes cache file to prevent auto-caching");

        // 4. Update .env to set a ROUTE_CACHE=false if needed
        if (!str_contains(File::get(base_path('.env')), 'ROUTE_CACHE=')) {
            File::append(base_path('.env'), "\nROUTE_CACHE=false\n");
            $this->info("Added ROUTE_CACHE=false to .env file");
        }
        
        $this->info('Route caching has been successfully disabled!');
        $this->info('You can run php artisan serve without route caching now.');
        
        return Command::SUCCESS;
    }
}
