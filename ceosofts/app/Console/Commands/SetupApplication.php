<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class SetupApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the application with SQLite database and required tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up the application...');

        // 1. Check if SQLite database exists
        $databasePath = database_path('database.sqlite');
        if (!File::exists($databasePath)) {
            $this->info('Creating SQLite database...');
            File::put($databasePath, '');
        }

        // 2. Clear all caches
        $this->info('Clearing caches...');
        Artisan::call('config:clear');
        $this->info(Artisan::output());
        
        Artisan::call('route:clear');
        $this->info(Artisan::output());
        
        Artisan::call('view:clear');
        $this->info(Artisan::output());

        // 3. Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->info(Artisan::output());

        // 4. Create storage link
        $this->info('Creating storage link...');
        Artisan::call('storage:link');
        $this->info(Artisan::output());

        $this->info('Application setup completed successfully!');
        
        // 5. Final instructions
        $this->info('You can now:');
        $this->info('1. Run "php artisan serve" to start the development server');
        $this->info('2. After setup, you can switch CACHE_STORE back to database in .env if needed');

        return Command::SUCCESS;
    }
}
