<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseConnectionTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test database connection and check required tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing database connection...');

        try {
            // Test connection
            $dbName = DB::connection()->getDatabaseName();
            $this->info("✓ Connected to database: {$dbName}");

            // Check units table
            if (Schema::hasTable('units')) {
                $unitsCount = DB::table('units')->count();
                $this->info("✓ Units table exists with {$unitsCount} records");

                // Check columns
                $columnsNeeded = ['unit_name_th', 'unit_name_en', 'description', 'is_active'];
                $missingColumns = [];
                foreach ($columnsNeeded as $column) {
                    if (!Schema::hasColumn('units', $column)) {
                        $missingColumns[] = $column;
                    }
                }

                if (!empty($missingColumns)) {
                    $this->warn("⚠️ Missing columns in units table: " . implode(", ", $missingColumns));
                    $this->info("Run php artisan migrate to add missing columns");
                } else {
                    $this->info("✓ All required columns exist in units table");
                }

                // Show table structure
                $this->info("Table structure:");
                $columns = Schema::getColumnListing('units');
                foreach ($columns as $column) {
                    $this->line("  - $column");
                }

                // Show sample records - check if we can display regardless of the column name
                if ($unitsCount > 0) {
                    $units = DB::table('units')->limit(3)->get();
                    $this->info("Sample units:");
                    
                    foreach ($units as $unit) {
                        // Check which column exists and use it
                        $nameColumn = property_exists($unit, 'unit_name_th') ? 'unit_name_th' : 
                                     (property_exists($unit, 'name') ? 'name' : null);
                        
                        if ($nameColumn) {
                            $this->line("  - ID: {$unit->id}, Name: {$unit->$nameColumn}");
                        } else {
                            $this->line("  - ID: {$unit->id}, Name column not found");
                        }
                    }
                } else {
                    $this->warn("⚠️ Units table is empty. Consider running seeder.");
                }
            } else {
                $this->error("✗ Units table does not exist!");
                $this->info("Try running: php artisan migrate");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Database error: " . $e->getMessage());
            $this->newLine();
            $this->warn("Stack trace:");
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}
