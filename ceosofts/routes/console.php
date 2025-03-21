<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use App\Models\{
    Invoice,
    Quotation,
    Product,
    Customer,
    Employee,
    Order,
    User,
    Attendance
};
use App\Mail\SystemReport;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Maintenance Commands
|--------------------------------------------------------------------------
*/

// คำสั่งแสดงคำคมสร้างแรงบันดาลใจ
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('แสดงคำคมสร้างแรงบันดาลใจ');

// Monitor logs with real-time updates
Artisan::command('logs:watch', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!File::exists($logFile)) {
        $this->error('ไม่พบไฟล์ log');
        return;
    }

    $this->info("เริ่มดูการเปลี่ยนแปลงของ log (กด Ctrl+C เพื่อหยุด)...");
    $lastSize = filesize($logFile);

    while (true) {
        clearstatcache();
        $currentSize = filesize($logFile);
        
        if ($currentSize > $lastSize) {
            $handle = fopen($logFile, 'r');
            fseek($handle, $lastSize);
            while ($line = fgets($handle)) {
                $this->line($line);
            }
            fclose($handle);
            $lastSize = $currentSize;
        }
        
        sleep(1);
    }
})->purpose('ดู log แบบ real-time');

// Clear system cache
Artisan::command('system:clear', function () {
    $this->info('กำลังล้างแคชระบบ...');
    
    try {
        Artisan::call('cache:clear');
        $this->line('✓ ล้าง cache เรียบร้อย');
        
        Artisan::call('config:clear');
        $this->line('✓ ล้าง config cache เรียบร้อย');
        
        Artisan::call('view:clear');
        $this->line('✓ ล้าง view cache เรียบร้อย');
        
        Artisan::call('route:clear');
        $this->line('✓ ล้าง route cache เรียบร้อย');
        
        Cache::flush();
        $this->line('✓ ล้าง application cache เรียบร้อย');
        
        if (function_exists('opcache_reset')) {
            opcache_reset();
            $this->line('✓ ล้าง OPcache เรียบร้อย');
        }
        
        $this->info('✅ ล้างแคชระบบเรียบร้อยแล้ว');
    } catch (\Exception $e) {
        $this->error('❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        Log::error('System clear error: ' . $e->getMessage());
    }
})->purpose('ล้างแคชระบบทั้งหมด');

// Database maintenance command
Artisan::command('db:maintenance', function () {
    $this->info('กำลังบำรุงรักษาฐานข้อมูล...');
    
    try {
        $this->comment('กำลังตรวจสอบตาราง...');
        
        $tables = DB::select('SHOW TABLES');
        $tableColumn = 'Tables_in_' . config('database.connections.mysql.database');
        
        foreach ($tables as $table) {
            $tableName = $table->$tableColumn;
            $this->line("กำลังตรวจสอบตาราง: {$tableName}");
            
            // Check and optimize table
            DB::statement("OPTIMIZE TABLE {$tableName}");
            DB::statement("ANALYZE TABLE {$tableName}");
            
            $this->line("✓ ปรับปรุงตาราง {$tableName} เรียบร้อย");
        }
        
        $this->info('✅ บำรุงรักษาฐานข้อมูลเสร็จสมบูรณ์');
    } catch (\Exception $e) {
        $this->error('❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        Log::error('Database maintenance error: ' . $e->getMessage());
    }
})->purpose('บำรุงรักษาฐานข้อมูลอัตโนมัติ');

/*
|--------------------------------------------------------------------------
| Business Report Commands
|--------------------------------------------------------------------------
*/

// Generate daily summary report
Artisan::command('report:daily {--date= : วันที่ต้องการดูรายงาน (YYYY-MM-DD)} {--email= : อีเมลที่ต้องการส่งรายงาน}', function () {
    $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
    $email = $this->option('email');
    
    $this->info("กำลังสร้างรายงานประจำวันที่ {$date->format('d/m/Y')}...");
    
    try {
        // ข้อมูลการขาย
        $dailySales = Invoice::whereDate('created_at', $date)->get();
        $totalSales = $dailySales->sum('total_amount');
        $paidSales = $dailySales->where('payment_status', 'paid')->sum('total_amount');
        
        // ข้อมูลคำสั่งซื้อ
        $dailyOrders = Order::whereDate('created_at', $date)->get();
        $totalOrders = $dailyOrders->count();
        
        // ข้อมูลลูกค้าใหม่
        $newCustomers = Customer::whereDate('created_at', $date)->count();
        
        // ข้อมูลพนักงานมาทำงาน
        $attendances = Attendance::whereDate('created_at', $date)->count();
        $employeeCount = Employee::where('status', 'active')->count();
        $attendanceRate = $employeeCount > 0 ? round(($attendances / $employeeCount) * 100, 2) : 0;
        
        $data = [
            ['หัวข้อ', 'จำนวน'],
            ['ยอดขายรวม', number_format($totalSales, 2) . ' บาท'],
            ['ยอดชำระแล้ว', number_format($paidSales, 2) . ' บาท'],
            ['จำนวนคำสั่งซื้อ', $totalOrders],
            ['ลูกค้าใหม่', $newCustomers],
            ['อัตราการมาทำงาน', $attendanceRate . '% (' . $attendances . '/' . $employeeCount . ')']
        ];
        
        $this->table($data[0], array_slice($data, 1));
        
        // Send email if requested
        if ($email) {
            $reportData = [
                'date' => $date->format('d/m/Y'),
                'sales' => number_format($totalSales, 2),
                'paid' => number_format($paidSales, 2),
                'orders' => $totalOrders,
                'newCustomers' => $newCustomers,
                'attendanceRate' => $attendanceRate
            ];
            
            Mail::to($email)->send(new SystemReport('รายงานประจำวัน', $reportData));
            $this->info("✅ ส่งรายงานไปยัง {$email} เรียบร้อยแล้ว");
        }
        
    } catch (\Exception $e) {
        $this->error('❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        Log::error('Daily report error: ' . $e->getMessage());
    }
})->purpose('สร้างรายงานสรุปประจำวัน');

// Check low stock products
Artisan::command('inventory:check-stock {--threshold=10 : ปริมาณขั้นต่ำที่ต้องการแจ้งเตือน} {--notify= : อีเมลที่ต้องการแจ้งเตือน}', function () {
    $threshold = (int) $this->option('threshold');
    $notifyEmail = $this->option('notify');
    
    $this->info("กำลังตรวจสอบสินค้าที่มีจำนวนน้อยกว่า {$threshold} ชิ้น...");
    
    try {
        $lowStockProducts = Product::where('stock_quantity', '<', $threshold)
            ->where('stock_quantity', '>', 0)
            ->get();
            
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->get();
        
        if ($lowStockProducts->isEmpty() && $outOfStockProducts->isEmpty()) {
            $this->info('✅ ไม่พบสินค้าที่ต้องเติมสต็อก');
            return;
        }
        
        if ($lowStockProducts->isNotEmpty()) {
            $this->warn("\nสินค้าที่ใกล้หมด:");
            $this->table(
                ['รหัส', 'ชื่อสินค้า', 'คงเหลือ'],
                $lowStockProducts->map(fn($p) => [
                    $p->code,
                    $p->name,
                    $p->stock_quantity
                ])
            );
        }
        
        if ($outOfStockProducts->isNotEmpty()) {
            $this->error("\nสินค้าที่หมดสต็อก:");
            $this->table(
                ['รหัส', 'ชื่อสินค้า'],
                $outOfStockProducts->map(fn($p) => [$p->code, $p->name])
            );
        }
        
        // Send email notification if requested
        if ($notifyEmail && ($lowStockProducts->isNotEmpty() || $outOfStockProducts->isNotEmpty())) {
            $reportData = [
                'low_stock' => $lowStockProducts->toArray(),
                'out_of_stock' => $outOfStockProducts->toArray(),
                'threshold' => $threshold,
                'date' => now()->format('d/m/Y')
            ];
            
            Mail::to($notifyEmail)->send(new SystemReport('แจ้งเตือนสินค้าคงเหลือน้อย', $reportData));
            $this->info("✅ ส่งการแจ้งเตือนไปยัง {$notifyEmail} เรียบร้อยแล้ว");
        }
        
    } catch (\Exception $e) {
        $this->error('❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        Log::error('Stock check error: ' . $e->getMessage());
    }
})->purpose('ตรวจสอบสินค้าที่มีจำนวนน้อย');

// Employee attendance summary
Artisan::command('hr:attendance-summary {--date= : วันที่ต้องการดูรายงาน (YYYY-MM-DD)}', function () {
    $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
    
    $this->info("สรุปการลงเวลาประจำวันที่ {$date->format('d/m/Y')}");
    
    try {
        $employees = Employee::with(['attendances' => function($q) use ($date) {
            $q->whereDate('created_at', $date);
        }])->get();
        
        $present = 0;
        $late = 0;
        $absent = 0;
        
        $data = $employees->map(function($employee) use (&$present, &$late, &$absent) {
            $attendance = $employee->attendances->first();
            
            if (!$attendance) {
                $absent++;
                $status = '🔴 ขาด';
            } else if ($attendance->is_late) {
                $late++;
                $status = '🟡 สาย';
            } else {
                $present++;
                $status = '🟢 มา';
            }
            
            return [
                'รหัส' => $employee->code,
                'ชื่อ' => $employee->name,
                'แผนก' => $employee->department->name ?? 'N/A',
                'เวลาเข้างาน' => $attendance ? $attendance->clock_in->format('H:i') : '-',
                'สถานะ' => $status
            ];
        });
        
        $this->table(['รหัส', 'ชื่อ', 'แผนก', 'เวลาเข้างาน', 'สถานะ'], $data);
        
        $this->info("\nสรุป:");
        $this->info("มาทำงาน: {$present}");
        $this->warn("มาสาย: {$late}");
        $this->error("ขาดงาน: {$absent}");
        
    } catch (\Exception $e) {
        $this->error('❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        Log::error('Attendance summary error: ' . $e->getMessage());
    }
})->purpose('แสดงรายงานสรุปการลงเวลาพนักงาน');

// Generate sales report
Artisan::command('report:sales {--from= : Start date (YYYY-MM-DD)} {--to= : End date (YYYY-MM-DD)} {--format=console : Output format (console/csv/pdf)} {--email= : Send report to email}', function () {
    $from = $this->option('from') ? Carbon::parse($this->option('from')) : Carbon::now()->startOfMonth();
    $to = $this->option('to') ? Carbon::parse($this->option('to')) : Carbon::now();
    $format = $this->option('format');
    $email = $this->option('email');

    $this->info("Generating sales report from {$from->format('Y-m-d')} to {$to->format('Y-m-d')}");
    
    try {
        // Get invoices in date range
        $invoices = Invoice::whereBetween('created_at', [$from, $to])->get();
        $totalSales = $invoices->sum('total_amount');
        $paidSales = $invoices->where('payment_status', 'paid')->sum('total_amount');
        $pendingSales = $invoices->where('payment_status', 'pending')->sum('total_amount');
        
        // Group by product category
        $salesByCategory = [];
        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $category = $item->product->category ?? 'Uncategorized';
                if (!isset($salesByCategory[$category])) {
                    $salesByCategory[$category] = 0;
                }
                $salesByCategory[$category] += $item->total_price;
            }
        }
        
        // Sort by sales volume
        arsort($salesByCategory);
        
        $data = [
            'period' => $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y'),
            'total_invoices' => $invoices->count(),
            'total_sales' => number_format($totalSales, 2),
            'paid_sales' => number_format($paidSales, 2),
            'pending_sales' => number_format($pendingSales, 2),
            'payment_rate' => $totalSales > 0 ? number_format(($paidSales / $totalSales) * 100, 2) . '%' : '0%'
        ];
        
        if ($format == 'csv') {
            $filename = storage_path('app/reports/sales_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv');
            
            // Create reports directory if it doesn't exist
            if (!File::exists(storage_path('app/reports'))) {
                File::makeDirectory(storage_path('app/reports'), 0755, true);
            }
            
            $file = fopen($filename, 'w');
            
            // Add headers
            fputcsv($file, array_keys($data));
            fputcsv($file, array_values($data));
            
            // Add category breakdown
            fputcsv($file, ['', '']);
            fputcsv($file, ['Category', 'Sales Amount']);
            foreach ($salesByCategory as $category => $sales) {
                fputcsv($file, [$category, number_format($sales, 2)]);
            }
            
            // Add detailed rows
            fputcsv($file, ['', '']);
            fputcsv($file, ['Invoice ID', 'Date', 'Customer', 'Amount', 'Status']);
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->id,
                    $invoice->created_at->format('Y-m-d'),
                    $invoice->customer->name ?? 'N/A',
                    number_format($invoice->total_amount, 2),
                    $invoice->payment_status
                ]);
            }
            
            fclose($file);
            $this->info("Sales report exported to: $filename");
            
            // Send email if requested
            if ($email) {
                Mail::to($email)->send(new SystemReport('รายงานยอดขาย', [
                    'period' => $data['period'],
                    'report_path' => $filename
                ]));
                $this->info("✅ ส่งรายงานไปยัง {$email} เรียบร้อยแล้ว");
            }
            
        } else {
            $this->table(array_keys($data), [array_values($data)]);
            
            if (!empty($salesByCategory)) {
                $this->info("\nSales by Category:");
                $categoryData = [];
                foreach ($salesByCategory as $category => $sales) {
                    $categoryData[] = [$category, number_format($sales, 2)];
                }
                $this->table(['Category', 'Sales Amount'], $categoryData);
            }
            
            $this->info("\nDetailed Invoices:");
            $tableData = $invoices->map(function ($invoice) {
                return [
                    'ID' => $invoice->id,
                    'Date' => $invoice->created_at->format('Y-m-d'),
                    'Customer' => $invoice->customer->name ?? 'N/A',
                    'Amount' => number_format($invoice->total_amount, 2),
                    'Status' => $invoice->payment_status
                ];
            })->toArray();
            
            $this->table(['ID', 'Date', 'Customer', 'Amount', 'Status'], $tableData);
        }
    } catch (\Exception $e) {
        $this->error("Error generating report: " . $e->getMessage());
        Log::error("Sales report error: " . $e->getMessage());
    }
})->purpose('Generate a sales report for a specific period');

// Check for quotations that are about to expire
Artisan::command('quotations:expiring {days=7 : Days before expiration} {--notify : Send notification to sales team}', function ($days) {
    $date = Carbon::now()->addDays($days);
    $notify = $this->option('notify');
    
    $this->info("Checking for quotations expiring in $days days (around {$date->format('Y-m-d')})");
    
    try {
        $quotations = Quotation::where('valid_until', '<=', $date)
            ->where('valid_until', '>=', Carbon::now())
            ->where('status', '!=', 'expired')
            ->get();
        
        if ($quotations->isEmpty()) {
            $this->info("No quotations expiring soon.");
            return;
        }
        
        $this->info("Found {$quotations->count()} quotations expiring soon:");
        
        $data = $quotations->map(function($quotation) {
            return [
                'ID' => $quotation->id,
                'Customer' => $quotation->customer->name ?? 'N/A',
                'Amount' => number_format($quotation->total_amount, 2),
                'Valid Until' => $quotation->valid_until->format('Y-m-d'),
                'Days Left' => $quotation->valid_until->diffInDays(Carbon::now()),
                'Sales Person' => $quotation->user->name ?? 'N/A'
            ];
        })->toArray();
        
        $this->table(['ID', 'Customer', 'Amount', 'Valid Until', 'Days Left', 'Sales Person'], $data);
        
        // Send notifications if requested
        if ($notify) {
            // Group quotations by salesperson
            $bySalesPerson = [];
            foreach ($quotations as $quotation) {
                $salesPersonId = $quotation->user_id ?? 0;
                if (!isset($bySalesPerson[$salesPersonId])) {
                    $bySalesPerson[$salesPersonId] = [];
                }
                $bySalesPerson[$salesPersonId][] = $quotation;
            }
            
            // Send notification to each salesperson
            foreach ($bySalesPerson as $salesPersonId => $salesQuotations) {
                if ($salesPersonId == 0) continue; // Skip if no salesperson assigned
                
                $salesPerson = User::find($salesPersonId);
                if (!$salesPerson || !$salesPerson->email) continue;
                
                Mail::to($salesPerson->email)->send(new SystemReport('แจ้งเตือนใบเสนอราคาใกล้หมดอายุ', [
                    'quotations' => $salesQuotations,
                    'days' => $days
                ]));
            }
            
            $this->info("✅ ส่งการแจ้งเตือนให้ทีมขายเรียบร้อยแล้ว");
        }
        
    } catch (\Exception $e) {
        $this->error("Error checking expiring quotations: " . $e->getMessage());
        Log::error("Expiring quotations error: " . $e->getMessage());
    }
})->purpose('List quotations that will expire in the specified number of days');

// Backup database
Artisan::command('db:backup {--filename= : Custom filename for the backup} {--include-files : Include storage files in backup}', function () {
    $filename = $this->option('filename') ?: 'ceosofts_backup_' . Carbon::now()->format('Y_m_d_His');
    $includeFiles = $this->option('include-files');
    $path = storage_path('app/backups');
    
    if (!File::exists($path)) {
        File::makeDirectory($path, 0755, true);
    }
    
    $this->info("Creating database backup...");
    
    try {
        $dbConfig = config('database.connections.' . config('database.default'));
        
        if ($dbConfig['driver'] == 'mysql') {
            // Create temporary directory for backup files
            $tempDir = $path . '/temp_' . time();
            File::makeDirectory($tempDir, 0755, true);
            
            // Backup database
            $dbFilename = $tempDir . '/database.sql';
            $command = sprintf(
                'mysqldump -h %s -u %s %s %s > %s',
                $dbConfig['host'],
                $dbConfig['username'],
                !empty($dbConfig['password']) ? '-p' . $dbConfig['password'] : '',
                $dbConfig['database'],
                $dbFilename
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new \Exception("Database backup failed");
            }
            
            // Include storage files if requested
            if ($includeFiles) {
                $this->info("Including storage files in backup...");
                $storagePath = storage_path('app/public');
                $storageDest = $tempDir . '/storage';
                
                if (File::exists($storagePath)) {
                    File::copyDirectory($storagePath, $storageDest);
                }
            }
            
            // Create zip archive
            $zip = new \ZipArchive();
            $zipName = $path . '/' . $filename . '.zip';
            
            if ($zip->open($zipName, \ZipArchive::CREATE) === TRUE) {
                // Add database backup
                $zip->addFile($dbFilename, 'database.sql');
                
                // Add metadata file with version and date
                $metaContent = json_encode([
                    'version' => config('app.version', '1.0.0'),
                    'date' => Carbon::now()->toIso8601String(),
                    'environment' => app()->environment(),
                ], JSON_PRETTY_PRINT);
                
                file_put_contents($tempDir . '/metadata.json', $metaContent);
                $zip->addFile($tempDir . '/metadata.json', 'metadata.json');
                
                // Add all files from temporary directory if including storage
                if ($includeFiles) {
                    $this->addFilesToZip($zip, $tempDir . '/storage', 'storage');
                }
                
                $zip->close();
                
                // Remove temporary directory
                File::deleteDirectory($tempDir);
                
                $this->info("Database backup created successfully: $zipName");
            } else {
                throw new \Exception("Could not create zip file");
            }
        } else {
            $this->error("Backup is only supported for MySQL databases");
        }
    } catch (\Exception $e) {
        $this->error("Backup failed: " . $e->getMessage());
        Log::error("Database backup error: " . $e->getMessage());
    }
})->purpose('Create a backup of the database');

// Helper function for adding files to zip recursively
function addFilesToZip(\ZipArchive $zip, $directory, $zipDirectory) {
    $files = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($directory),
        \RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($directory) + 1);
            
            $zip->addFile($filePath, $zipDirectory . '/' . $relativePath);
        }
    }
}

// Clean old backups
Artisan::command('backup:clean {--days=30 : Number of days to keep backups}', function () {
    $days = (int) $this->option('days');
    $backupPath = storage_path('app/backups');
    
    if (!File::exists($backupPath)) {
        $this->info("No backups directory found.");
        return;
    }
    
    $files = File::files($backupPath);
    $now = Carbon::now();
    $deleted = 0;
    
    foreach ($files as $file) {
        $modifiedTime = Carbon::createFromTimestamp(File::lastModified($file));
        if ($now->diffInDays($modifiedTime) > $days) {
            File::delete($file);
            $deleted++;
        }
    }
    
    $this->info("Cleaned up {$deleted} old backup files.");
})->purpose('Clean up old backup files');

// System health check
Artisan::command('system:health', function () {
    $this->info('Running system health check...');
    
    // Format bytes helper function
    $formatBytes = function ($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    };
    
    // Check PHP version and extensions
    $this->comment('Checking PHP configuration...');
    $phpVersion = phpversion();
    $this->line(" - PHP Version: {$phpVersion}");
    
    $requiredExtensions = ['pdo', 'mbstring', 'openssl', 'json', 'curl', 'xml', 'zip', 'gd'];
    
    foreach ($requiredExtensions as $ext) {
        $loaded = extension_loaded($ext);
        $this->line(" - {$ext} extension: " . ($loaded ? '✓' : '✗'));
    }
    
    // Check disk space
    $this->comment('Checking disk space...');
    $totalSpace = disk_total_space(base_path());
    $freeSpace = disk_free_space(base_path());
    $usedSpace = $totalSpace - $freeSpace;
    $usedPercent = round(($usedSpace / $totalSpace) * 100, 2);
    
    $this->line(" - Total disk space: " . $formatBytes($totalSpace));
    $this->line(" - Free disk space: " . $formatBytes($freeSpace));
    $this->line(" - Used disk space: " . $formatBytes($usedSpace) . " ({$usedPercent}%)");
    
    if ($usedPercent > 90) {
        $this->warn(" ⚠️ Disk space usage is high!");
    }
    
    // Check database connection
    $this->comment('Checking database connection...');
    try {
        DB::connection()->getPdo();
        $this->line(" - Database connection: ✓");
        
        // Check tables
        $this->line(" - Database tables:");
        $tables = DB::select('SHOW TABLES');
        $tableColumn = 'Tables_in_' . config('database.connections.mysql.database');
        foreach ($tables as $table) {
            $this->line("   - {$table->$tableColumn}");
        }
        
    } catch (\Exception $e) {
        $this->error(" - Database connection: ✗ (" . $e->getMessage() . ")");
    }
})->purpose('Check system health');
