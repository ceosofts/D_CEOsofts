<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a comprehensive system health check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Beginning system health check...');
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database connection successful');
        } catch (\Exception $e) {
            $this->error('✗ Database connection failed: ' . $e->getMessage());
            Log::error('Database connection failed: ' . $e->getMessage());
        }
        
        // Check file permissions
        $storagePath = storage_path();
        if (is_writable($storagePath)) {
            $this->info('✓ Storage directory is writable');
        } else {
            $this->error('✗ Storage directory is not writable');
            Log::warning('Storage directory is not writable');
        }
        
        // Check cache accessibility
        try {
            cache()->put('health_check_test', true, 1);
            if (cache()->get('health_check_test') === true) {
                $this->info('✓ Cache system is working');
            } else {
                $this->error('✗ Cache system failed to retrieve value');
            }
        } catch (\Exception $e) {
            $this->error('✗ Cache system error: ' . $e->getMessage());
            Log::error('Cache system error: ' . $e->getMessage());
        }
        
        $this->info('Health check completed.');
    }
}
