<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class CleanupCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:cleanup
                            {--a|all : ล้างแคชทั้งหมด รวมถึงแคชแอพพลิเคชัน แคชรูปแบบ และ OPcache}
                            {--f|force : ข้ามการยืนยัน}
                            {--temp : ล้างไฟล์ชั่วคราวในโฟลเดอร์ storage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ล้างแคชระบบทั้งหมดเพื่อเพิ่มประสิทธิภาพการทำงาน';

    /**
     * Storage directories to clean.
     *
     * @var array
     */
    protected $storageDirs = [
        'logs' => '*.log', 
        'framework/cache' => '*',
        'framework/views' => '*',
        'framework/sessions' => '*',
        'app/temp' => '*',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check for confirmation unless --force is used
        if (!$this->option('force') && 
            !$this->confirm('การล้างแคชจะลบข้อมูลชั่วคราวทั้งหมดและอาจส่งผลกระทบต่อประสิทธิภาพในช่วงแรก คุณต้องการดำเนินการต่อหรือไม่?')) {
            $this->info('ยกเลิกการล้างแคช');
            return 0;
        }

        $this->components->info('กำลังเริ่มล้างแคชระบบ...');

        $startTime = microtime(true);
        $step = 1;
        $success = true;

        // Clear Laravel caches
        $this->components->task("[$step] ล้างแคชคอนฟิกเกอเรชัน", function() {
            return $this->callSilent('config:clear') === 0;
        });
        $step++;

        $this->components->task("[$step] ล้างแคชเส้นทาง", function() {
            return $this->callSilent('route:clear') === 0;
        });
        $step++;

        $this->components->task("[$step] ล้างแคชมุมมอง", function() {
            return $this->callSilent('view:clear') === 0;
        });
        $step++;

        $this->components->task("[$step] ล้างแคชแอพพลิเคชัน", function() {
            try {
                Cache::flush();
                return true;
            } catch (\Exception $e) {
                $this->error('ไม่สามารถล้าง Cache: ' . $e->getMessage());
                Log::error('Cache clear error: ' . $e->getMessage());
                return false;
            }
        });
        $step++;

        // Only clear compiled if --all option is provided
        if ($this->option('all')) {
            $this->components->task("[$step] ล้างไฟล์คอมไพล์", function() {
                return $this->callSilent('clear-compiled') === 0;
            });
            $step++;
            
            // Clear OPcache if available and requested
            $this->components->task("[$step] ล้าง OPcache", function() {
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                    return true;
                }
                $this->warn('OPcache ไม่ได้เปิดใช้งานบนเซิร์ฟเวอร์นี้');
                return true;
            });
            $step++;
        }

        // Clear temporary files if requested
        if ($this->option('temp')) {
            $this->components->task("[$step] ล้างไฟล์ชั่วคราว", function() {
                return $this->cleanTemporaryFiles();
            });
            $step++;
        }

        // Optimize again
        $this->components->task("[$step] ปรับแต่งประสิทธิภาพแอพพลิเคชัน", function() {
            return $this->callSilent('optimize') === 0;
        });

        $executionTime = number_format(microtime(true) - $startTime, 2);
        
        $this->newLine();
        $this->components->info("ล้างแคชระบบสำเร็จ (ใช้เวลา $executionTime วินาที)");
        
        return 0;
    }

    /**
     * Clean temporary files in storage directory
     *
     * @return bool
     */
    protected function cleanTemporaryFiles(): bool
    {
        try {
            $totalCleaned = 0;
            $storagePath = storage_path();
            
            foreach ($this->storageDirs as $dir => $pattern) {
                $path = $storagePath . '/' . $dir;
                
                if (!File::exists($path)) {
                    continue;
                }
                
                // Find files matching pattern and not essential system files
                $finder = new Finder();
                $finder->files()->in($path)->name($pattern);
                
                // Don't delete .gitignore files or important system files
                $finder->notName(['.gitignore', '.gitkeep', '*.gitkeep']);
                
                // For logs, keep the last 3 days of log files
                if ($dir === 'logs') {
                    $thresholdDate = now()->subDays(3)->format('Y-m-d');
                    $finder->filter(function (\SplFileInfo $file) use ($thresholdDate) {
                        // Only delete older log files
                        $filename = $file->getFilename();
                        if (preg_match('/laravel-(\d{4}-\d{2}-\d{2})/', $filename, $matches)) {
                            return $matches[1] < $thresholdDate;
                        }
                        return true;
                    });
                }
                
                foreach ($finder as $file) {
                    File::delete($file->getRealPath());
                    $totalCleaned++;
                }
            }
            
            $this->line("  <fg=gray>ล้าง {$totalCleaned} ไฟล์ชั่วคราว</>");
            return true;
        } catch (\Exception $e) {
            Log::error('Error cleaning temporary files: ' . $e->getMessage());
            return false;
        }
    }
}
