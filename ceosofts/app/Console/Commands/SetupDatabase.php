<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-database {--fresh : Drop all tables and re-run all migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up database with proper structure and seed data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up database...');
        
        // 1. Check if we're using SQLite
        if (config('database.default') === 'sqlite') {
            $this->setupSqliteDatabase();
        }
        
        // 2. Run migrations
        $this->runMigrations();
        
        // 3. Seed database
        $this->seedDatabase();
        
        // 4. Create symbolic link for storage
        $this->createStorageLink();
        
        // 5. Optimize the application
        $this->optimizeApplication();
        
        $this->info('Database setup completed successfully!');
        $this->info("\nAvailable user accounts:");
        $this->table(
            ['Email', 'Password', 'Role'],
            [
                ['admin@example.com', 'password', 'Admin'],
                ['manager@example.com', 'password', 'Manager'],
                ['user@example.com', 'password', 'User'],
            ]
        );
        
        return Command::SUCCESS;
    }
    
    /**
     * Set up SQLite database
     */
    protected function setupSqliteDatabase()
    {
        $databasePath = database_path('database.sqlite');
        
        if (!File::exists($databasePath)) {
            $this->info('Creating SQLite database file...');
            File::put($databasePath, '');
            
            // Set proper permissions
            chmod($databasePath, 0644);
            $this->info("SQLite database file created at: {$databasePath}");
        } else {
            $this->info("SQLite database file already exists.");
        }
    }
    
    /**
     * Run database migrations
     */
    protected function runMigrations()
    {
        $this->info('Running migrations...');
        
        try {
            if ($this->option('fresh')) {
                $this->info('Dropping all tables and re-running migrations...');
                Artisan::call('migrate:fresh', ['--force' => true]);
            } else {
                Artisan::call('migrate', ['--force' => true]);
            }
            
            $this->info(Artisan::output());
        } catch (\Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
            
            if (config('database.default') === 'sqlite') {
                $this->info("\nFor SQLite, ensure the database file is writable:");
                $this->line("chmod 644 " . database_path('database.sqlite'));
                $this->line("chown www-data:www-data " . database_path('database.sqlite'));
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Seed the database
     */
    protected function seedDatabase()
    {
        $this->info('Seeding database...');
        
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $this->info(Artisan::output());
        } catch (\Exception $e) {
            $this->error("Seeding failed: " . $e->getMessage());
            
            // Try to seed individually
            $this->info("\nTrying to seed individually...");
            $seeders = [
                'RolePermissionSeeder',
                'DepartmentSeeder',
                'PositionSeeder',
                'PrefixSeeder',
                'CompanySeeder',
                'UnitSeeder',
                'ItemStatusSeeder',
                'UserSeeder',
                'EmployeeSeeder',
                'ProductsTableSeeder',
                'CustomersTableSeeder',
                'OrdersTableSeeder',
                'JobStatusSeeder',
                'PaymentStatusSeeder',
                'TaxSettingSeeder'
            ];
            
            foreach ($seeders as $seeder) {
                $this->line("Seeding: {$seeder}");
                try {
                    Artisan::call('db:seed', [
                        '--class' => "Database\\Seeders\\{$seeder}",
                        '--force' => true
                    ]);
                } catch (\Exception $seedEx) {
                    $this->warn("Failed to seed {$seeder}: " . $seedEx->getMessage());
                }
            }
        }
        
        return true;
    }
    
    /**
     * Create symbolic link for storage
     */
    protected function createStorageLink()
    {
        $this->info('Creating storage link...');
        
        try {
            if (!file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
                $this->info('Storage link created successfully.');
            } else {
                $this->info('Storage link already exists.');
            }
        } catch (\Exception $e) {
            $this->warn("Failed to create storage link: " . $e->getMessage());
        }
    }
    
    /**
     * Optimize application
     */
    protected function optimizeApplication()
    {
        $this->info('Optimizing application...');
        
        try {
            // Clear and cache config
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            
            // Clear and cache routes
            Artisan::call('route:clear');
            Artisan::call('route:cache');
            
            // Clear view cache
            Artisan::call('view:clear');
            
            $this->info('Application optimized successfully.');
        } catch (\Exception $e) {
            $this->warn("Optimization error: " . $e->getMessage());
        }
    }
}
