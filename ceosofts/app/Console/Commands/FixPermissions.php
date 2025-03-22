<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix permissions for storage and cache directories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting correct permissions for storage and cache directories...');
        
        $directories = [
            // Storage directories
            storage_path(),
            storage_path('app'),
            storage_path('app/public'),
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
            
            // Bootstrap cache
            base_path('bootstrap/cache'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("Created directory: {$directory}");
            }
            
            // Set proper permissions
            chmod($directory, 0755);
            $this->info("Set permissions for: {$directory}");
        }
        
        // For cache data directories specifically
        $cacheData = storage_path('framework/cache/data');
        if (File::exists($cacheData)) {
            $this->info("Setting cache data files to be writable");
            
            foreach (File::allFiles($cacheData) as $file) {
                chmod($file->getPathname(), 0644);
            }
        }

        $this->info('All permissions fixed successfully!');
        return Command::SUCCESS;
    }
}
