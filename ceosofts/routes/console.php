<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Quotation;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Inspire Command
|--------------------------------------------------------------------------
|
| This command will display an inspiring quote when executed.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Application Maintenance Commands
|--------------------------------------------------------------------------
*/

// Monitor application logs
Artisan::command('logs:tail {--lines=50 : Number of lines to display}', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!File::exists($logFile)) {
        $this->error('Log file does not exist.');
        return;
    }

    $lines = (int) $this->option('lines');
    $this->info("Displaying the last {$lines} lines of the log file:");
    $this->line(File::get($logFile, -1 * $lines));
})->purpose('View the last few lines of the application log');

// Clear old logs
Artisan::command('logs:clear {--days=30 : Number of days to keep logs}', function () {
    $days = (int) $this->option('days');
    $logPath = storage_path('logs');
    $files = File::files($logPath);

    $this->info("Clearing logs older than {$days} days...");
    $deleted = 0;

    foreach ($files as $file) {
        if (now()->diffInDays(File::lastModified($file)) > $days) {
            File::delete($file);
            $deleted++;
        }
    }

    $this->info("Deleted {$deleted} old log files.");
})->purpose('Clear log files older than a specified number of days');

// Check system dependencies
Artisan::command('system:check', function () {
    $this->info('Checking system dependencies...');

    $dependencies = [
        'PHP >= 8.0' => version_compare(PHP_VERSION, '8.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'Mbstring Extension' => extension_loaded('mbstring'),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'BCMath Extension' => extension_loaded('bcmath'),
        'Ctype Extension' => extension_loaded('ctype'),
        'JSON Extension' => extension_loaded('json'),
        'XML Extension' => extension_loaded('xml'),
    ];

    foreach ($dependencies as $dependency => $status) {
        $this->line($dependency . ': ' . ($status ? '✅' : '❌'));
    }

    if (in_array(false, $dependencies)) {
        $this->error('Some dependencies are missing. Please install them to ensure the application works correctly.');
    } else {
        $this->info('All dependencies are satisfied.');
    }
})->purpose('Check if all required system dependencies are installed');

/*
|--------------------------------------------------------------------------
| CEOSofts Business Commands
|--------------------------------------------------------------------------
*/

// Generate sales report
Artisan::command('report:sales {--from= : Start date (YYYY-MM-DD)} {--to= : End date (YYYY-MM-DD)} {--format=console : Output format (console/csv)}', function () {
    $from = $this->option('from') ? Carbon::parse($this->option('from')) : Carbon::now()->startOfMonth();
    $to = $this->option('to') ? Carbon::parse($this->option('to')) : Carbon::now();
    $format = $this->option('format');

    $this->info("Generating sales report from {$from->format('Y-m-d')} to {$to->format('Y-m-d')}");
    
    try {
        // Get invoices in date range
        $invoices = Invoice::whereBetween('created_at', [$from, $to])->get();
        $totalSales = $invoices->sum('total_amount');
        $paidSales = $invoices->where('payment_status', 'paid')->sum('total_amount');
        $pendingSales = $invoices->where('payment_status', 'pending')->sum('total_amount');
        
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
            $file = fopen($filename, 'w');
            
            // Add headers
            fputcsv($file, array_keys($data));
            fputcsv($file, array_values($data));
            
            // Add detailed rows
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
        } else {
            $this->table(array_keys($data), [array_values($data)]);
            
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
Artisan::command('quotations:expiring {days=7 : Days before expiration}', function ($days) {
    $date = Carbon::now()->addDays($days);
    
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
                'Days Left' => $quotation->valid_until->diffInDays(Carbon::now())
            ];
        })->toArray();
        
        $this->table(['ID', 'Customer', 'Amount', 'Valid Until', 'Days Left'], $data);
    } catch (\Exception $e) {
        $this->error("Error checking expiring quotations: " . $e->getMessage());
        Log::error("Expiring quotations error: " . $e->getMessage());
    }
})->purpose('List quotations that will expire in the specified number of days');

// Backup database
Artisan::command('db:backup {--filename= : Custom filename for the backup}', function () {
    $filename = $this->option('filename') ?: 'ceosofts_backup_' . Carbon::now()->format('Y_m_d_His');
    $path = storage_path('app/backups');
    
    if (!File::exists($path)) {
        File::makeDirectory($path, 0755, true);
    }
    
    $this->info("Creating database backup...");
    
    try {
        $dbConfig = config('database.connections.' . config('database.default'));
        
        if ($dbConfig['driver'] == 'mysql') {
            $command = sprintf(
                'mysqldump -h %s -u %s %s %s > %s/%s.sql',
                $dbConfig['host'],
                $dbConfig['username'],
                !empty($dbConfig['password']) ? '-p' . $dbConfig['password'] : '',
                $dbConfig['database'],
                $path,
                $filename
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new \Exception("Database backup failed");
            }
            
            // Create zip archive
            $zip = new \ZipArchive();
            $zipName = $path . '/' . $filename . '.zip';
            
            if ($zip->open($zipName, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($path . '/' . $filename . '.sql', $filename . '.sql');
                $zip->close();
                
                // Remove the SQL file
                File::delete($path . '/' . $filename . '.sql');
                
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
