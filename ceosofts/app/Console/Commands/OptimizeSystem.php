<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\Process\Process;

class OptimizeSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:optimize
                            {--m|maintenance : ทำงานในโหมดบำรุงรักษา (จะปิดระบบชั่วคราว)}
                            {--schedule : ทำงานจาก task scheduler (ไม่แสดงผลลัพธ์)}
                            {--all : ดำเนินการทั้งหมด (รวมการจัดการฐานข้อมูล)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ปรับแต่งประสิทธิภาพและบำรุงรักษาระบบแบบครบวงจร';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = microtime(true);
        $silent = $this->option('schedule');

        // Show header if not in silent mode
        if (!$silent) {
            $this->components->info('เริ่มการปรับแต่งระบบ CEOSOFTS...');
            $this->newLine();
        }

        // Enter maintenance mode if requested
        if ($this->option('maintenance')) {
            $this->components->task('🔒 เปิดโหมดบำรุงรักษา', function() {
                Artisan::call('down', ['--render' => 'errors.maintenance']);
                return true;
            });
        }

        // Always run these optimizations
        $this->performBasicOptimizations();

        // Database maintenance if requested
        if ($this->option('all')) {
            $this->performDatabaseMaintenance();
        }

        // Clean storage if in full optimization
        if ($this->option('all')) {
            $this->performStorageCleaning();
        }

        // Exit maintenance mode if it was entered
        if ($this->option('maintenance')) {
            $this->components->task('🔓 ปิดโหมดบำรุงรักษา', function() {
                Artisan::call('up');
                return true;
            });
        }

        // Final output
        $duration = round(microtime(true) - $start, 2);
        
        if (!$silent) {
            $this->newLine();
            $this->components->info("🎉 การปรับแต่งระบบสำเร็จ (ใช้เวลา {$duration} วินาที)");
            
            // Get memory usage
            $memUsage = $this->getMemoryUsage();
            $this->line("<fg=gray>การใช้หน่วยความจำ: {$memUsage}</>");
        }
        
        // Log completion
        Log::info("System optimization completed in $duration seconds");
        
        return 0;
    }

    /**
     * Perform basic Laravel optimizations
     */
    protected function performBasicOptimizations()
    {
        // Clear all caches
        $this->components->task('🧹 ล้างแคชระบบ', function() {
            Artisan::call('cache:cleanup', ['--all' => true, '--force' => true]);
            return true;
        });

        // Optimize routes
        $this->components->task('🛣️ ปรับแต่งเส้นทาง', function() {
            Artisan::call('route:cache');
            return true;
        });

        // Optimize config
        $this->components->task('⚙️ ปรับแต่งคอนฟิกเกอเรชัน', function() {
            Artisan::call('config:cache');
            return true;
        });

        // Optimize class loading
        $this->components->task('📚 ปรับแต่งการโหลดคลาส', function() {
            Artisan::call('optimize');
            return true;
        });
    }

    /**
     * Perform database maintenance tasks
     */
    protected function performDatabaseMaintenance()
    {
        $this->components->task('🔄 บำรุงรักษาฐานข้อมูล', function() {
            try {
                if (config('database.default') !== 'mysql') {
                    $this->warn('Database maintenance is only supported for MySQL.');
                    return true;
                }

                $tables = DB::select('SHOW TABLES');
                $tableColumn = 'Tables_in_' . config('database.connections.mysql.database');
                
                foreach ($tables as $table) {
                    $tableName = $table->$tableColumn;
                    DB::statement("ANALYZE TABLE `{$tableName}`");
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                }
                
                return true;
            } catch (\Exception $e) {
                Log::error("Database maintenance failed: " . $e->getMessage());
                return false;
            }
        });
        
        // Clean up old records if needed (example with soft-deleted records)
        $this->components->task('🗑️ ล้างข้อมูลเก่าที่ไม่จำเป็น', function() {
            try {
                // Sample cleanup queries - customize for your actual tables
                $threshold = Carbon::now()->subMonths(6);
                
                // Example: Permanently delete soft-deleted records older than 6 months
                // DB::table('your_table')->whereNotNull('deleted_at')
                //     ->where('deleted_at', '<', $threshold)
                //     ->delete();

                // Example: Delete old logs
                // DB::table('logs')->where('created_at', '<', $threshold)->delete();
                
                // For now, we'll just log that this ran
                Log::info("Old record cleanup ran successfully");
                
                return true;
            } catch (\Exception $e) {
                Log::error("Old record cleanup failed: " . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Clean storage directories
     */
    protected function performStorageCleaning()
    {
        $this->components->task('📦 ทำความสะอาดพื้นที่เก็บข้อมูล', function() {
            try {
                // Clean logs older than 30 days
                $logPath = storage_path('logs');
                $oldLogs = glob($logPath . '/*.log');
                $threshold = Carbon::now()->subDays(30);
                
                $deleted = 0;
                foreach ($oldLogs as $log) {
                    if (File::lastModified($log) < $threshold->timestamp) {
                        File::delete($log);
                        $deleted++;
                    }
                }
                
                // Clean temporary uploads
                $tempPath = storage_path('app/temp');
                if (File::exists($tempPath)) {
                    $tempFiles = File::files($tempPath);
                    foreach ($tempFiles as $file) {
                        File::delete($file);
                    }
                }
                
                return true;
            } catch (\Exception $e) {
                Log::error("Storage cleaning failed: " . $e->getMessage());
                return false;
            }
        });

        // Backup if in full maintenance mode
        if ($this->option('maintenance')) {
            $this->components->task('💾 สร้างไฟล์สำรองข้อมูล', function() {
                try {
                    $result = Artisan::call('db:backup', [
                        '--filename' => 'auto_maintenance_backup_' . date('Y_m_d_His'),
                    ]);
                    return $result === 0;
                } catch (\Exception $e) {
                    Log::error("Backup during maintenance failed: " . $e->getMessage());
                    return false;
                }
            });
        }
    }

    /**
     * Get formatted memory usage
     */
    protected function getMemoryUsage(): string
    {
        $memory = memory_get_usage(true);
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        return @round($memory / pow(1024, ($i = floor(log($memory, 1024)))), 2) . ' ' . $unit[$i];
    }
}
