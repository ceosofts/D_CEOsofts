<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    DashboardController,
    CustomerController,
    ProductController,
    OrderController,
    OrderItemController,
    EmployeeController,
    CompanyHolidayController,
    AttendanceController,
    WorkShiftController,
    PayrollController,
    WageController,
    QuotationController,
    InvoiceController,
    ReportController,
    ProfileController,
    CalendarController,
    NotificationController,
    LeaveRequestController,
    ReceiptController
};
use App\Http\Controllers\Admin\{
    UserController,
    DepartmentController,
    CompanyController,
    UnitController,
    PositionController,
    PrefixController,
    ItemStatusController,
    PaymentStatusController,
    TaxSettingController,
    JobStatusController
};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ThaiPdfService;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', fn() => view('welcome'))->name('welcome');

// Authentication Routes with verification
Auth::routes(['verify' => true, 'register' => config('auth.registration_enabled', true)]);

// PDF Test route
Route::get('/test-pdf', function () {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>ทดสอบภาษาไทย</title>
        <style>
            @font-face {
                font-family: "thsarabunnew";
                src: url("' . storage_path('fonts/THSarabunNew.ttf') . '");
                font-weight: normal;
                font-style: normal;
            }
            * {
                font-family: "thsarabunnew";
            }
            body {
                font-size: 16pt;
                line-height: 1.5;
                margin: 0;
                padding: 20px;
            }
            .thai-text {
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="thai-text">
            <h1>ทดสอบการแสดงผลภาษาไทย</h1>
            <p>ทดสอบภาษาไทยขนาดปกติ</p>
            <p>Thai Language Test / ทดสอบ</p>
            <p>1234567890 ๑๒๓๔๕๖๗๘๙๐</p>
        </div>
    </body>
    </html>
    ';

    $pdfService = app(ThaiPdfService::class);
    return $pdfService->generatePdf($html);
})->middleware('auth')->name('pdf.test');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Redirect /home to dashboard
    Route::get('/home', fn() => redirect()->route('dashboard'))->name('home');

    // Dashboard (requires department middleware)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('department')
        ->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function() {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/activity-log', [ProfileController::class, 'activityLog'])->name('activity-log');
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (สำหรับผู้ใช้ที่มี role: admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // System Management
        Route::resources([
            'companies' => CompanyController::class,
            'users' => UserController::class,
            'departments' => DepartmentController::class,
            'units' => UnitController::class,
            'positions' => PositionController::class,
            'prefixes' => PrefixController::class,
            'item_statuses' => ItemStatusController::class,
            'payment_statuses' => PaymentStatusController::class,
            'tax' => TaxSettingController::class,
            'job-statuses' => JobStatusController::class,
        ]);

        // System maintenance routes
        Route::prefix('system')->name('system.')->group(function() {
            Route::get('/maintenance', [DashboardController::class, 'maintenance'])->name('maintenance');
            Route::post('/clear-cache', function() {
                try {
                    Cache::flush();
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('view:clear');
                    Artisan::call('route:clear');
                    return redirect()->back()->with('success', 'ล้างแคชเรียบร้อยแล้ว');
                } catch (\Exception $e) {
                    Log::error('Error clearing cache: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการล้างแคช: ' . $e->getMessage());
                }
            })->name('clear-cache');
            Route::get('/logs', [DashboardController::class, 'viewLogs'])->name('logs');
            Route::get('/backup', [DashboardController::class, 'backup'])->name('backup');
            Route::get('/php-info', [DashboardController::class, 'phpInfo'])->name('php-info');
            
            // เพิ่มเส้นทางสำหรับการปรับปรุงฐานข้อมูล
            Route::post('/migrate', function() {
                try {
                    Artisan::call('migrate', ['--force' => true]);
                    $output = Artisan::output();
                    return redirect()->back()->with('success', 'อัพเกรดฐานข้อมูลเรียบร้อยแล้ว: ' . $output);
                } catch (\Exception $e) {
                    Log::error('Error migrating: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
                }
            })->name('migrate');
            
            // ตั้งค่าระบบ
            Route::get('/settings', [DashboardController::class, 'systemSettings'])->name('settings');
            Route::put('/settings', [DashboardController::class, 'updateSystemSettings'])->name('settings.update');
        });

        // หน้าจัดการการอนุญาต (Permissions Management)
        Route::prefix('permissions')->name('permissions.')->group(function() {
            Route::get('/', [UserController::class, 'permissions'])->name('index');
            Route::post('/sync', [UserController::class, 'syncPermissions'])->name('sync');
            Route::post('/assign', [UserController::class, 'assignRolePermission'])->name('assign');
            Route::delete('/revoke', [UserController::class, 'revokeRolePermission'])->name('revoke');
        });
        
        // รายงานสำหรับผู้ดูแลระบบ
        Route::prefix('reports')->name('reports.')->group(function() {
            Route::get('/users-activity', [ReportController::class, 'usersActivity'])->name('users-activity');
            Route::get('/system-health', [ReportController::class, 'systemHealth'])->name('system-health');
            Route::get('/database-size', [ReportController::class, 'databaseSize'])->name('database-size');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Resource Routes (สำหรับผู้ใช้ที่มี department)
    |--------------------------------------------------------------------------
    */
    Route::middleware('department')->group(function () {

        // Customer Management
        Route::resource('customers', CustomerController::class);
        Route::prefix('customers')->name('customers.')->group(function() {
            // Customer lookup by code
            Route::prefix('code/{code}')->group(function () {
                Route::get('/', [CustomerController::class, 'showByCode'])->name('showByCode');
                Route::get('/edit', [CustomerController::class, 'editByCode'])->name('editByCode');
                Route::put('/', [CustomerController::class, 'updateByCode'])->name('updateByCode');
                Route::delete('/', [CustomerController::class, 'destroyByCode'])->name('destroyByCode');
            });
            
            // Customer search and API routes
            Route::get('/search', [CustomerController::class, 'search'])->name('search');
            Route::get('/api/list', [CustomerController::class, 'apiList'])->name('api.list');
            
            // เพิ่มเส้นทางใหม่
            Route::get('/export', [CustomerController::class, 'export'])->name('export');
            Route::post('/import', [CustomerController::class, 'import'])->name('import');
            Route::get('/{customer}/history', [CustomerController::class, 'history'])->name('history');
        });

        // Products
        Route::resource('products', ProductController::class);
        Route::prefix('products')->name('products.')->group(function() {
            Route::get('/search', [ProductController::class, 'search'])->name('search');
            Route::get('/api/list', [ProductController::class, 'apiList'])->name('api.list');
            Route::get('/export', [ProductController::class, 'export'])->name('export');
            Route::post('/import', [ProductController::class, 'import'])->name('import');
            
            // เพิ่มเส้นทางใหม่
            Route::post('/bulk-update', [ProductController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('low-stock');
            Route::get('/categories', [ProductController::class, 'categories'])->name('categories');
            Route::get('/{product}/history', [ProductController::class, 'priceHistory'])->name('price-history');
        });
        
        // Orders & Order Items
        Route::resource('orders', OrderController::class);
        Route::resource('orders.items', OrderItemController::class)->parameters([
            'items' => 'order_item'
        ]); // แก้ไขชื่อ parameter
        Route::prefix('orders')->name('orders.')->group(function() {
            Route::get('/{order}/pdf', [OrderController::class, 'generatePDF'])->name('pdf');
            Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('status');
            
            // เพิ่มเส้นทางใหม่
            Route::get('/search', [OrderController::class, 'search'])->name('search');
            Route::get('/pending', [OrderController::class, 'pendingOrders'])->name('pending');
            Route::post('/{order}/confirm', [OrderController::class, 'confirmOrder'])->name('confirm');
            Route::get('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
        });
        
        // HR Management
        Route::prefix('hr')->name('hr.')->group(function() {
            // Resources
            Route::resources([
                'employees' => EmployeeController::class,
                'holidays' => CompanyHolidayController::class, // แก้ไขให้เป็น holidays
                'shifts' => WorkShiftController::class, // แก้ไขให้เป็น shifts
                'attendances' => AttendanceController::class, // ย้าย attendances มาที่นี่
                'leave-requests' => LeaveRequestController::class, // แก้ไขให้ถูกต้อง
            ]);
            
            // Employee additional routes
            Route::prefix('employees')->name('employees.')->group(function() {
                Route::get('/{employee}/documents', [EmployeeController::class, 'documents'])->name('documents');
                Route::post('/{employee}/documents', [EmployeeController::class, 'uploadDocument'])->name('documents.upload');
                Route::delete('/{employee}/documents/{document}', [EmployeeController::class, 'deleteDocument'])->name('documents.delete');
                
                // เพิ่มเส้นทางใหม่
                Route::get('/export', [EmployeeController::class, 'export'])->name('export');
                Route::post('/import', [EmployeeController::class, 'import'])->name('import');
                Route::get('/{employee}/performance', [EmployeeController::class, 'performance'])->name('performance');
                Route::get('/{employee}/attendance-record', [EmployeeController::class, 'attendanceRecord'])->name('attendance-record');
            });
            
            // Attendance Routes (ย้าย middleware ไปที่ resource registration)
            Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])->name('my-attendance');
            Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
            Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
            
            // Company holidays
            Route::prefix('holidays')->name('holidays.')->group(function() {
                Route::get('/calendar', [CompanyHolidayController::class, 'calendar'])->name('calendar');
                Route::post('/import', [CompanyHolidayController::class, 'import'])->name('import');
                Route::get('/export', [CompanyHolidayController::class, 'export'])->name('export');
            });
                
            // Wages & Payroll
            Route::prefix('wages')->name('wages.')->group(function() {
                Route::get('/', [WageController::class, 'index'])->name('summary');
                Route::post('/store-monthly', [WageController::class, 'storeMonthlyWages'])->name('storeMonthly');
                Route::get('/api', [WageController::class, 'getWageData'])->name('api');
                Route::get('/report', [WageController::class, 'report'])->name('report');
                Route::get('/export', [WageController::class, 'export'])->name('export');
                
                // เพิ่มเส้นทางใหม่
                Route::post('/approve/{employee}', [WageController::class, 'approve'])->name('approve');
                Route::post('/reject/{employee}', [WageController::class, 'reject'])->name('reject');
            });
            
            Route::prefix('payroll')->name('payroll.')->group(function() {
                Route::get('/', [PayrollController::class, 'index'])->name('index');
                Route::get('/create', [PayrollController::class, 'create'])->name('create');
                Route::post('/', [PayrollController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [PayrollController::class, 'edit'])->name('edit');
                Route::put('/{id}', [PayrollController::class, 'update'])->name('update');
                Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('destroy');
                Route::get('/slip/{id}', [PayrollController::class, 'showSlip'])->name('slip');
                Route::get('/slip/{id}/pdf', [PayrollController::class, 'downloadSlipPdf'])->name('slip.pdf');
                Route::get('/check', [PayrollController::class, 'checkPayroll'])->name('check');
                Route::get('/{id}/pdf', [PayrollController::class, 'generatePDF'])->name('pdf');
                Route::get('/export', [PayrollController::class, 'exportAll'])->name('export');
                Route::get('/summary', [PayrollController::class, 'summary'])->name('summary');
                
                // เพิ่มเส้นทางใหม่
                Route::post('/bulk-generate', [PayrollController::class, 'bulkGenerate'])->name('bulk-generate');
                Route::post('/bulk-approve', [PayrollController::class, 'bulkApprove'])->name('bulk-approve');
                Route::get('/tax-summary', [PayrollController::class, 'taxSummary'])->name('tax-summary');
                Route::get('/tax-summary/export', [PayrollController::class, 'exportTaxSummary'])->name('tax-summary.export');
            });
            
            // Leave Requests
            Route::prefix('leave-requests')->name('leave-requests.')->group(function() {
                Route::get('/my-requests', [LeaveRequestController::class, 'myRequests'])->name('my-requests');
                Route::get('/pending', [LeaveRequestController::class, 'pendingRequests'])->name('pending');
                Route::post('/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('approve');
                Route::post('/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('reject');
                Route::get('/summary', [LeaveRequestController::class, 'summary'])->name('summary');
                Route::get('/calendar', [LeaveRequestController::class, 'calendar'])->name('calendar');
            });
        });

        // Sales Management
        Route::prefix('sales')->name('sales.')->group(function() {
            // Quotations
            Route::resource('quotations', QuotationController::class);
            Route::prefix('quotations')->name('quotations.')->group(function() {
                Route::get('/{quotation}/export', [QuotationController::class, 'export'])->name('export');
                Route::get('/{quotation}/create-invoice', [InvoiceController::class, 'createFromQuotation'])
                    ->name('create-invoice')
                    ->middleware('can:create invoice');
                Route::post('/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('status');
                Route::get('/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('duplicate');
                Route::get('/search', [QuotationController::class, 'search'])->name('search');
                
                // เพิ่มเส้นทางใหม่
                Route::get('/expired', [QuotationController::class, 'expired'])->name('expired');
                Route::get('/approved', [QuotationController::class, 'approved'])->name('approved');
                Route::get('/pending', [QuotationController::class, 'pending'])->name('pending');
                Route::post('/{quotation}/send-email', [QuotationController::class, 'sendEmail'])->name('send-email');
                Route::post('/bulk-status', [QuotationController::class, 'bulkUpdateStatus'])->name('bulk-status');
            });
                
            // Invoices
            Route::resource('invoices', InvoiceController::class);
            Route::prefix('invoices')->name('invoices.')->group(function() {
                Route::get('/{invoice}/pdf', [InvoiceController::class, 'generatePDF'])->name('pdf');
                Route::post('/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-paid');
                Route::post('/{invoice}/send-email', [InvoiceController::class, 'sendInvoiceEmail'])->name('send-email');
                Route::get('/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('duplicate');
                Route::get('/search', [InvoiceController::class, 'search'])->name('search');
                Route::get('/overdue', [InvoiceController::class, 'overdue'])->name('overdue');
                
                // เพิ่มเส้นทางใหม่
                Route::get('/paid', [InvoiceController::class, 'paid'])->name('paid');
                Route::get('/unpaid', [InvoiceController::class, 'unpaid'])->name('unpaid');
                Route::get('/due-this-week', [InvoiceController::class, 'dueThisWeek'])->name('due-this-week');
                Route::post('/bulk-reminder', [InvoiceController::class, 'bulkSendReminder'])->name('bulk-reminder');
                Route::get('/{invoice}/payment-history', [InvoiceController::class, 'paymentHistory'])->name('payment-history');
            });
            
            // Receipts
            Route::resource('receipts', ReceiptController::class);
            Route::prefix('receipts')->name('receipts.')->group(function() {
                Route::get('/{receipt}/pdf', [ReceiptController::class, 'generatePDF'])->name('pdf');
                Route::post('/{receipt}/send-email', [ReceiptController::class, 'sendEmail'])->name('send-email');
                Route::get('/search', [ReceiptController::class, 'search'])->name('search');
            });
            
            // Sales Dashboard
            Route::get('/dashboard', [ReportController::class, 'salesDashboard'])->name('dashboard');
            
            // เพิ่มเส้นทางใหม่
            Route::get('/my-commission', [ReportController::class, 'myCommission'])->name('my-commission');
            Route::get('/targets', [ReportController::class, 'salesTargets'])->name('targets');
            Route::get('/customer-analysis', [ReportController::class, 'customerAnalysis'])->name('customer-analysis');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->middleware('can:view reports')->group(function() {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/sales/export', [ReportController::class, 'exportSalesReport'])->name('sales.export');
            Route::get('/quotations', [ReportController::class, 'quotations'])->name('quotations');
            Route::get('/invoices', [ReportController::class, 'invoices'])->name('invoices');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/products', [ReportController::class, 'products'])->name('products');
            Route::get('/product-sales', [ReportController::class, 'productSales'])->name('product-sales');
            Route::get('/employee-performance', [ReportController::class, 'employeePerformance'])
                ->name('employee-performance')
                ->middleware('role:admin,manager');
                
            // เพิ่มเส้นทางใหม่
            Route::get('/revenue-by-month', [ReportController::class, 'revenueByMonth'])->name('revenue-by-month');
            Route::get('/profit-analysis', [ReportController::class, 'profitAnalysis'])->name('profit-analysis');
            Route::get('/tax-reports', [ReportController::class, 'taxReports'])->name('tax-reports');
            Route::get('/inventory-value', [ReportController::class, 'inventoryValue'])->name('inventory-value');
            Route::get('/receivables-aging', [ReportController::class, 'receivablesAging'])->name('receivables-aging');
            Route::post('/custom-report', [ReportController::class, 'customReport'])->name('custom-report');
        });

        // Calendar routes for scheduling
        Route::prefix('calendar')->name('calendar.')->group(function() {
            Route::get('/', [CalendarController::class, 'index'])->name('index');
            Route::get('/events', [CalendarController::class, 'getEvents'])->name('events');
            Route::post('/events', [CalendarController::class, 'storeEvent'])->name('events.store');
            Route::put('/events/{id}', [CalendarController::class, 'updateEvent'])->name('events.update');
            Route::delete('/events/{id}', [CalendarController::class, 'deleteEvent'])->name('events.destroy');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function() {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/preferences', [NotificationController::class, 'preferences'])->name('preferences');
            Route::put('/preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');
        });
    });
});

// Health check endpoint for monitoring services
Route::get('/health-check', function() {
    return response()->json([
        'status' => 'ok', 
        'timestamp' => now()->toIso8601String(),
        'app_version' => config('app.version', '1.0.0'),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'environment' => app()->environment()
    ]);
})->name('health.check');

// Fallback route when URL does not exist
Route::fallback(function() {
    return response()->view('errors.404', [], 404);
});

// Don't cache routes in local development
if (app()->environment('production')) {
    Artisan::call('route:cache');
}
