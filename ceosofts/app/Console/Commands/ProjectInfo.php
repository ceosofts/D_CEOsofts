<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ProjectInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display information about the CEOsofts project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->components->info('CEOsofts Project Information');
        
        // Project Information
        $this->components->twoColumnDetail('<fg=green;options=bold>Project name</>', 'CEOsofts');
        $this->components->twoColumnDetail('<fg=green;options=bold>Laravel version</>', app()->version());
        $this->components->twoColumnDetail('<fg=green;options=bold>PHP version</>', PHP_VERSION);
        $this->components->twoColumnDetail('<fg=green;options=bold>Database</>', config('database.default'));
        
        // Environment
        $this->components->twoColumnDetail('<fg=green;options=bold>Environment</>', app()->environment());
        $this->components->twoColumnDetail('<fg=green;options=bold>Debug mode</>', config('app.debug') ? 'Enabled' : 'Disabled');
        
        // Routes information
        $this->newLine();
        $this->components->info('Routes Information');
        $routesCount = count(\Illuminate\Support\Facades\Route::getRoutes());
        $this->components->twoColumnDetail('<fg=green;options=bold>Total routes</>', $routesCount);
        
        // Features
        $this->newLine();
        $this->components->info('Available Features');
        
        $features = [
            'Customer Management',
            'Product Management',
            'Order Management',
            'HR Management (Employees, Attendance)',
            'Invoicing and Quotations',
            'Payroll System',
            'Reporting',
        ];
        
        foreach ($features as $feature) {
            $this->line(" - <fg=blue>{$feature}</>");
        }
        
        // Database stats
        $this->newLine();
        $this->components->info('Database Statistics');
        
        try {
            $tables = $this->getDatabaseTables();
            $this->components->twoColumnDetail('<fg=green;options=bold>Database tables</>', count($tables));
            
            // Display some key table counts
            $this->displayTableCounts();
            
        } catch (\Exception $e) {
            $this->error("Could not connect to database: " . $e->getMessage());
        }
        
        // Usage instructions
        $this->newLine();
        $this->components->info('Usage Instructions');
        $this->line(" - <fg=yellow>php artisan app:setup-database</> - Set up database with sample data");
        $this->line(" - <fg=yellow>php artisan app:fix-permissions</> - Fix storage permissions");
        $this->line(" - <fg=yellow>php artisan app:setup-frontend</> - Set up frontend assets");
        $this->line(" - <fg=yellow>php artisan route:disable-cache</> - Disable route caching (for development)");
        
        $this->newLine();
        $this->line("For full list of available commands, refer to the <fg=cyan>คำสั่ง.txt</> file.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get the list of database tables
     */
    protected function getDatabaseTables()
    {
        $tables = [];
        
        switch (config('database.default')) {
            case 'sqlite':
                $results = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($results as $result) {
                    $tables[] = $result->name;
                }
                break;
                
            case 'mysql':
                $results = DB::select('SHOW TABLES');
                foreach ($results as $result) {
                    $tables[] = array_values(get_object_vars($result))[0];
                }
                break;
                
            default:
                $tables = ['Unknown database type'];
        }
        
        return $tables;
    }
    
    /**
     * Display counts for important tables
     */
    protected function displayTableCounts()
    {
        $tables = [
            'users' => 'Users',
            'departments' => 'Departments',
            'products' => 'Products',
            'customers' => 'Customers',
            'roles' => 'Roles',
            'permissions' => 'Permissions',
        ];
        
        foreach ($tables as $table => $label) {
            try {
                $count = DB::table($table)->count();
                $this->components->twoColumnDetail("<fg=green;options=bold>{$label}</>", $count);
            } catch (\Exception $e) {
                // Skip if table doesn't exist
            }
        }
    }
}
