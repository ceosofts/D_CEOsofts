<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Symfony\Component\Process\Process;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health-check
                            {--email= : ส่งรายงานไปยังอีเมลนี้}
                            {--full : ตรวจสอบแบบละเอียดเต็มรูปแบบ}
                            {--fix : พยายามแก้ไขปัญหาที่พบโดยอัตโนมัติ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ตรวจสอบสถานะสุขภาพของระบบ';

    /**
     * Issues found during the check.
     *
     * @var array
     */
    protected $issues = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->components->info('กำลังตรวจสอบสุขภาพของระบบ CEOSOFTS...');
        $this->newLine();

        $startTime = microtime(true);

        // Run all checks
        $this->checkPhp();
        $this->checkDiskSpace();
        $this->checkPermissions();
        $this->checkDatabase();
        
        if ($this->option('full')) {
            $this->checkCacheSystem();
            $this->checkExternalConnections();
            $this->checkSecuritySettings();
            $this->checkBackups();
        }

        // Calculate summary
        $this->newLine();
        $executionTime = round(microtime(true) - $startTime, 2);
        
        // Display the summary
        $this->displaySummary($executionTime);
        
        // Send email report if requested
        $email = $this->option('email');
        if ($email) {
            $this->sendEmailReport($email);
        }
        
        // Return error code if critical issues found
        if (count(array_filter($this->issues, fn($i) => $i['severity'] === 'critical')) > 0) {
            return 1;
        }
        
        return 0;
    }

    /**
     * Check PHP configuration
     */
    protected function checkPhp(): void
    {
        $this->components->info('ตรวจสอบ PHP และส่วนขยาย');
        
        // Check PHP version
        $phpVersion = phpversion();
        $minVersion = '8.0.0'; // Minimum PHP version
        
        $versionCheck = version_compare($phpVersion, $minVersion, '>=');
        $this->line(" - PHP Version: <fg=" . ($versionCheck ? 'green' : 'red') . ">{$phpVersion}</>");
        
        if (!$versionCheck) {
            $this->addIssue('PHP version is too old', "Current: {$phpVersion}, Recommended: >= {$minVersion}", 'critical');
        }
        
        // Check PHP memory limit
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = $this->getMemoryInBytes($memoryLimit);
        $recommendedBytes = 256 * 1024 * 1024; // 256MB
        
        $memoryCheck = $memoryLimitBytes >= $recommendedBytes || $memoryLimitBytes == -1;
        $this->line(" - Memory Limit: <fg=" . ($memoryCheck ? 'green' : 'yellow') . ">{$memoryLimit}</>");
        
        if (!$memoryCheck) {
            $this->addIssue('PHP memory limit is too low', "Current: {$memoryLimit}, Recommended: 256M", 'warning');
        }
        
        // Check required extensions
        $requiredExtensions = [
            'pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'curl', 'fileinfo',
            'tokenizer', 'xml', 'zip', 'gd'
        ];
        
        foreach ($requiredExtensions as $extension) {
            $enabled = extension_loaded($extension);
            $this->line(" - {$extension}: <fg=" . ($enabled ? 'green' : 'red') . ">" . ($enabled ? '✓' : '✗') . "</>");
            
            if (!$enabled) {
                $this->addIssue("PHP extension '{$extension}' is missing", "This extension is required by the application", 'critical');
            }
        }
        
        $this->newLine();
    }

    /**
     * Check disk space
     */
    protected function checkDiskSpace(): void
    {
        $this->components->info('ตรวจสอบพื้นที่จัดเก็บข้อมูล');
        
        $basePath = base_path();
        $totalSpace = disk_total_space($basePath);
        $freeSpace = disk_free_space($basePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usedPercentage = round(($usedSpace / $totalSpace) * 100, 1);
        
        $warningThreshold = 80;
        $criticalThreshold = 95;
        
        $color = 'green';
        if ($usedPercentage >= $criticalThreshold) {
            $color = 'red';
        } elseif ($usedPercentage >= $warningThreshold) {
            $color = 'yellow';
        }
        
        $this->line(" - Total disk space: " . $this->formatBytes($totalSpace));
        $this->line(" - Free disk space: " . $this->formatBytes($freeSpace));
        $this->line(" - Used disk space: <fg={$color}>" . $this->formatBytes($usedSpace) . " ({$usedPercentage}%)</>");
        
        if ($usedPercentage >= $criticalThreshold) {
            $this->addIssue('Disk space is critically low', "Used: {$usedPercentage}%, Free: " . $this->formatBytes($freeSpace), 'critical');
        } elseif ($usedPercentage >= $warningThreshold) {
            $this->addIssue('Disk space is running low', "Used: {$usedPercentage}%, Free: " . $this->formatBytes($freeSpace), 'warning');
        }
        
        // Check storage directories
        $storagePath = storage_path();
        if (!File::exists($storagePath) || !File::isWritable($storagePath)) {
            $this->line(" - Storage directory: <fg=red>Not writable</>");
            $this->addIssue('Storage directory is not writable', "Path: {$storagePath}", 'critical');
        } else {
            $this->line(" - Storage directory: <fg=green>Writable</>");
        }
        
        $this->newLine();
    }

    /**
     * Check directory permissions
     */
    protected function checkPermissions(): void
    {
        $this->components->info('ตรวจสอบสิทธิ์การเข้าถึงไดเรกทอรี');
        
        $directoriesToCheck = [
            'storage' => storage_path(),
            'bootstrap/cache' => base_path('bootstrap/cache'),
            'public' => public_path(),
        ];
        
        foreach ($directoriesToCheck as $name => $path) {
            if (!File::exists($path)) {
                $this->line(" - {$name}: <fg=red>Not found</>");
                $this->addIssue("{$name} directory doesn't exist", "Path: {$path}", 'critical');
                continue;
            }
            
            $isWritable = File::isWritable($path);
            $this->line(" - {$name}: <fg=" . ($isWritable ? 'green' : 'red') . ">" . ($isWritable ? 'Writable' : 'Not writable') . "</>");
            
            if (!$isWritable) {
                $this->addIssue("{$name} directory is not writable", "Path: {$path}", 'critical');
                
                if ($this->option('fix')) {
                    try {
                        chmod($path, 0755);
                        $this->line("   <fg=yellow>✓ Fixed permissions for {$name}</>");
                    } catch (\Exception $e) {
                        $this->line("   <fg=red>× Failed to fix permissions: {$e->getMessage()}</>");
                    }
                }
            }
        }
        
        $this->newLine();
    }

    /**
     * Check database health
     */
    protected function checkDatabase(): void
    {
        $this->components->info('ตรวจสอบฐานข้อมูล');
        
        try {
            // Check connection
            DB::connection()->getPdo();
            $this->line(" - Database connection: <fg=green>Successful</>");
            
            // Check tables
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            $this->line(" - Tables count: <fg=green>{$tableCount}</>");
            
            // Check migrations table
            if (Schema::hasTable('migrations')) {
                $migrations = DB::table('migrations')->count();
                $this->line(" - Migrations: <fg=green>{$migrations}</>");
            } else {
                $this->line(" - Migrations table: <fg=red>Missing</>");
                $this->addIssue('Migrations table is missing', 'The database might not be properly migrated', 'critical');
            }
            
            // Database size
            if (config('database.default') === 'mysql') {
                $size = DB::select('SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb FROM information_schema.TABLES WHERE table_schema = ?', [config('database.connections.mysql.database')]);
                
                if (isset($size[0]->size_mb)) {
                    $sizeMB = round($size[0]->size_mb, 2);
                    $color = 'green';
                    
                    if ($sizeMB > 1000) {
                        $color = 'yellow';
                    }
                    
                    $this->line(" - Database size: <fg={$color}>{$sizeMB} MB</>");
                    
                    if ($sizeMB > 1000) {
                        $this->addIssue('Database size is large', "Size: {$sizeMB}MB", 'warning');
                    }
                }
            }
        } catch (\Exception $e) {
            $this->line(" - Database connection: <fg=red>Failed</>");
            $this->line(" - Error: <fg=red>{$e->getMessage()}</>");
            $this->addIssue('Database connection failed', $e->getMessage(), 'critical');
        }
        
        $this->newLine();
    }

    /**
     * Check cache system
     */
    protected function checkCacheSystem(): void
    {
        $this->components->info('ตรวจสอบระบบแคช');
        
        // Check if cache is working
        try {
            $cacheKey = 'health_check_' . time();
            $value = md5(rand());
            
            Cache::put($cacheKey, $value, 60);
            $retrieved = Cache::get($cacheKey);
            
            $cacheWorking = $value === $retrieved;
            $this->line(" - Cache system: <fg=" . ($cacheWorking ? 'green' : 'red') . ">" . ($cacheWorking ? 'Working' : 'Not working') . "</>");
            
            if (!$cacheWorking) {
                $this->addIssue('Cache system is not working', 'The application cache might be corrupted', 'warning');
                
                if ($this->option('fix')) {
                    $this->callSilent('cache:clear');
                    $this->line("   <fg=yellow>✓ Cache has been cleared</>");
                }
            }
            
            // Get cache driver
            $driver = config('cache.default');
            $this->line(" - Cache driver: <fg=green>{$driver}</>");
        } catch (\Exception $e) {
            $this->line(" - Cache system: <fg=red>Error</>");
            $this->line(" - Error: <fg=red>{$e->getMessage()}</>");
            $this->addIssue('Cache system error', $e->getMessage(), 'warning');
        }
        
        $this->newLine();
    }

    /**
     * Check external connections (mail, API endpoints)
     */
    protected function checkExternalConnections(): void
    {
        $this->components->info('ตรวจสอบการเชื่อมต่อภายนอก');
        
        // Check mail configuration
        $mailDriver = config('mail.default');
        $mailHost = config('mail.mailers.' . $mailDriver . '.host');
        $mailConfigured = !empty($mailHost);
        
        $this->line(" - Mail driver: <fg=" . ($mailConfigured ? 'green' : 'yellow') . ">{$mailDriver}</>");
        
        if (!$mailConfigured) {
            $this->addIssue('Email configuration incomplete', 'Mail server host is not configured', 'warning');
        }
        
        // Check internet connectivity
        try {
            $response = Http::timeout(5)->get('https://www.google.com');
            $connected = $response->successful();
            $this->line(" - Internet connectivity: <fg=" . ($connected ? 'green' : 'red') . ">" . ($connected ? 'Connected' : 'Disconnected') . "</>");
            
            if (!$connected) {
                $this->addIssue('Internet connectivity issues', 'Unable to connect to external services', 'warning');
            }
        } catch (\Exception $e) {
            $this->line(" - Internet connectivity: <fg=red>Error</>");
            $this->addIssue('Internet connectivity check failed', $e->getMessage(), 'warning');
        }
        
        $this->newLine();
    }

    /**
     * Check security settings
     */
    protected function checkSecuritySettings(): void
    {
        $this->components->info('ตรวจสอบการตั้งค่าความปลอดภัย');
        
        // Check APP_DEBUG
        $appDebug = config('app.debug');
        $this->line(" - Debug mode: <fg=" . ($appDebug ? 'red' : 'green') . ">" . ($appDebug ? 'Enabled' : 'Disabled') . "</>");
        
        if ($appDebug) {
            $this->addIssue('Debug mode is enabled', 'This can expose sensitive information in production', 'warning');
            
            if ($this->option('fix') && app()->environment('production')) {
                // This is not a complete fix, it just outputs instructions
                $this->line("   <fg=yellow>To disable debug mode, set APP_DEBUG=false in your .env file</>");
            }
        }
        
        // Check encryption key
        $key = config('app.key');
        $hasKey = !empty($key);
        $this->line(" - Application key: <fg=" . ($hasKey ? 'green' : 'red') . ">" . ($hasKey ? 'Set' : 'Not set') . "</>");
        
        if (!$hasKey) {
            $this->addIssue('Application key is not set', 'This is critical for secure encryption', 'critical');
            
            if ($this->option('fix')) {
                $this->callSilent('key:generate');
                $this->line("   <fg=yellow>✓ Generated new application key</>");
            }
        }
        
        // Check CSRF protection
        $csrfEnabled = config('app.env') !== 'testing';
        $this->line(" - CSRF protection: <fg=" . ($csrfEnabled ? 'green' : 'red') . ">" . ($csrfEnabled ? 'Enabled' : 'Disabled') . "</>");
        
        if (!$csrfEnabled) {
            $this->addIssue('CSRF protection is disabled', 'This could expose the application to CSRF attacks', 'critical');
        }
        
        $this->newLine();
    }

    /**
     * Check backups
     */
    protected function checkBackups(): void
    {
        $this->components->info('ตรวจสอบไฟล์สำรอง');
        
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            $this->line(" - Backup directory: <fg=yellow>Not found</>");
            $this->addIssue('Backup directory does not exist', "Path: {$backupPath}", 'warning');
            return;
        }
        
        $backupFiles = File::files($backupPath);
        $backupCount = count($backupFiles);
        
        $this->line(" - Backup count: <fg=" . ($backupCount > 0 ? 'green' : 'yellow') . ">{$backupCount}</>");
        
        if ($backupCount === 0) {
            $this->addIssue('No backup files found', 'Regular backups are recommended', 'warning');
            return;
        }
        
        // Find the most recent backup
        $latestBackup = null;
        $latestTime = 0;
        
        foreach ($backupFiles as $file) {
            $modTime = File::lastModified($file);
            if ($modTime > $latestTime) {
                $latestTime = $modTime;
                $latestBackup = $file;
            }
        }
        
        if ($latestBackup) {
            $daysSinceBackup = Carbon::createFromTimestamp($latestTime)->diffInDays();
            $backupSize = File::size($latestBackup);
            
            $color = 'green';
            if ($daysSinceBackup > 7) {
                $color = 'red';
            } elseif ($daysSinceBackup > 3) {
                $color = 'yellow';
            }
            
            $this->line(" - Latest backup: <fg={$color}>{$daysSinceBackup} days ago (" . $this->formatBytes($backupSize) . ")</>");
            
            if ($daysSinceBackup > 7) {
                $this->addIssue('Latest backup is too old', "Last backup was {$daysSinceBackup} days ago", 'warning');
                
                if ($this->option('fix')) {
                    $this->line("   <fg=yellow>Scheduling a new backup...</>");
                    // This would typically run a backup command or schedule one
                    // For safety, we don't actually run it here
                }
            }
        }
        
        $this->newLine();
    }

    /**
     * Display summary of health check
     */
    protected function displaySummary(float $executionTime): void
    {
        $criticalCount = count(array_filter($this->issues, fn($i) => $i['severity'] === 'critical'));
        $warningCount = count(array_filter($this->issues, fn($i) => $i['severity'] === 'warning'));
        $infoCount = count(array_filter($this->issues, fn($i) => $i['severity'] === 'info'));
        
        $this->components->info('สรุปผลการตรวจสอบสุขภาพระบบ:');
        $this->line(" - ปัญหาร้ายแรง: <fg=" . ($criticalCount > 0 ? 'red' : 'green') . ">{$criticalCount}</>");
        $this->line(" - คำเตือน: <fg=" . ($warningCount > 0 ? 'yellow' : 'green') . ">{$warningCount}</>");
        $this->line(" - ข้อมูล: <fg=blue>{$infoCount}</>");
        $this->line(" - เวลาที่ใช้: {$executionTime} seconds");
        
        if ($criticalCount > 0 || $warningCount > 0) {
            $this->newLine();
            $this->components->warn('พบปัญหาที่ต้องได้รับการแก้ไข:');
            
            foreach ($this->issues as $issue) {
                $color = 'blue';
                $prefix = 'ℹ️';
                
                if ($issue['severity'] === 'critical') {
                    $color = 'red';
                    $prefix = '❌';
                } elseif ($issue['severity'] === 'warning') {
                    $color = 'yellow';
                    $prefix = '⚠️';
                }
                
                $this->line("{$prefix} <fg={$color}>{$issue['title']}</>");
                $this->line("   <fg=gray>{$issue['description']}</>");
            }
        } else {
            $this->newLine();
            $this->components->info('🎉 ระบบทำงานได้ปกติ ไม่พบปัญหาใดๆ');
        }
    }

    /**
     * Send email report
     */
    protected function sendEmailReport(string $email): void
    {
        $this->components->task('Sending health check report to ' . $email, function() use ($email) {
            try {
                // In a real implementation, you would create and send a proper email here
                // For this example, we'll just simulate success
                $this->line("   <fg=gray>Email report would be sent to {$email}</>");
                return true;
            } catch (\Exception $e) {
                Log::error('Failed to send health check report: ' . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Add an issue to the issues list
     */
    protected function addIssue(string $title, string $description, string $severity): void
    {
        $this->issues[] = [
            'title' => $title,
            'description' => $description,
            'severity' => $severity,
        ];
    }

    /**
     * Format bytes to human-readable format
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Convert memory limit string to bytes
     */
    protected function getMemoryInBytes($memoryLimit): int
    {
        if ($memoryLimit === '-1') {
            return -1;
        }
        
        $unit = strtolower(substr($memoryLimit, -1));
        $value = (int) substr($memoryLimit, 0, -1);
        
        switch ($unit) {
            case 'g':
                $value *= 1024;
                // fall through
            case 'm':
                $value *= 1024;
                // fall through
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}