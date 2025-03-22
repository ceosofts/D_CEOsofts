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
    TestLoginController // Already imported here
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
use App\Http\Controllers\TestController;
use App\Services\ThaiPdfService;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Define a basic route for the homepage
Route::get('/', function () {
    return view('welcome');
})->name('welcome'); // Changed from 'home' to 'welcome'

// Authentication Routes with rate limiting to prevent brute force attacks
Route::middleware(['throttle:login'])->group(function () {
    Auth::routes();
});

// Add this route to test the navbar
Route::get('/test-navbar', function () {
    return view('test-navbar');
})->name('test.navbar');

// Add this route to test pure Bootstrap navbar
Route::get('/pure-bootstrap', function () {
    return view('pure-bootstrap');
})->name('pure.bootstrap');

// Test routes - add these to public routes, outside of auth middleware
// เพิ่ม route สำหรับทดสอบการเข้าสู่ระบบ (เฉพาะ development เท่านั้น)
if (app()->environment('local')) {
    Route::get('/test-users', [TestLoginController::class, 'showUsers']);
    Route::post('/test-login-attempt', [TestLoginController::class, 'testLogin']);
}

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Redirect /home to dashboard
    Route::get('/home', [DashboardController::class, 'redirectToDashboard'])->name('home');

    // Dashboard (requires department middleware)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['department', 'verified'])
        ->name('dashboard');

    // Profile routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (สำหรับผู้ใช้ที่มี role: admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Home
        Route::get('/', [DashboardController::class, 'adminHome']);
        
        // Add explicit route for admin dashboard
        Route::get('/dashboard', [DashboardController::class, 'adminHome'])->name('dashboard');

        // Resource Management
        Route::resources([
            'companies'        => CompanyController::class,
            'users'            => UserController::class,
            'departments'      => DepartmentController::class,
            'units'            => UnitController::class,
            'positions'        => PositionController::class,
            'item_statuses'    => ItemStatusController::class,
            'payment_statuses' => PaymentStatusController::class,
            'tax'              => TaxSettingController::class,
            'job-statuses'     => JobStatusController::class,
        ]);

        // เพิ่มการลงทะเบียน Route แบบเจาะจงสำหรับ Prefixes (ใช้แบบนี้เท่านั้น)
        Route::get('prefixes', [PrefixController::class, 'index'])->name('prefixes.index');
        Route::get('prefixes/create', [PrefixController::class, 'create'])->name('prefixes.create');
        Route::post('prefixes', [PrefixController::class, 'store'])->name('prefixes.store');
        Route::get('prefixes/{id}/edit', [PrefixController::class, 'edit'])->name('prefixes.edit');
        Route::put('prefixes/{id}', [PrefixController::class, 'update'])->name('prefixes.update');
        Route::delete('prefixes/{id}', [PrefixController::class, 'destroy'])->name('prefixes.destroy');
        Route::get('prefixes/{id}', [PrefixController::class, 'show'])->name('prefixes.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Department Routes (สำหรับผู้ใช้ที่มี department)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['department'])->group(function () {
        // Customer Management
        Route::resource('customers', CustomerController::class);
        Route::prefix('customers/code/{code}')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'showByCode'])->name('showByCode');
            Route::get('/edit', [CustomerController::class, 'editByCode'])->name('editByCode');
            Route::put('/', [CustomerController::class, 'updateByCode'])->name('updateByCode');
            Route::delete('/', [CustomerController::class, 'destroyByCode'])->name('destroyByCode');
        });

        // Product Management
        Route::resource('products', ProductController::class);

        // Order Management
        Route::resource('orders', OrderController::class);
        Route::resource('orders.order-items', OrderItemController::class);

        // HR Management
        Route::resource('employees', EmployeeController::class);
        Route::resource('company-holidays', CompanyHolidayController::class);
        Route::resource('work-shifts', WorkShiftController::class);

        // Attendance Management (Admin only)
        Route::resource('attendances', AttendanceController::class)
            ->middleware('role:admin');

        // Quotation Management
        Route::resource('quotations', QuotationController::class);
        Route::get('quotations/{quotation}/export', [QuotationController::class, 'export'])
            ->name('quotations.export');

        // Create invoice from quotation
        Route::get('quotations/{quotation}/create-invoice', [InvoiceController::class, 'createFromQuotation'])
            ->name('quotations.create-invoice')
            ->middleware('can:create invoice');

        // Remove the duplicate route registration entirely - don't define the resource twice
        // Invoice Management - With specific routes
        // The specific routes are defined first, then the resource
        Route::get('invoice/{invoice}/pdf', [InvoiceController::class, 'generatePDF'])->name('invoice.pdf');
        Route::post('invoice/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoice.mark-paid');

        // Now define the resource routes AFTER the specific routes
        Route::resource('invoices', InvoiceController::class);

        // Wage & Payroll Management
        Route::prefix('wages')->name('wages.')->group(function () {
            Route::get('/summary', [WageController::class, 'index'])->name('summary');
            Route::post('/store-monthly', [WageController::class, 'storeMonthlyWages'])->name('storeMonthly');
        });

        Route::prefix('payrolls')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::get('/create', [PayrollController::class, 'create'])->name('create');
            Route::post('/', [PayrollController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PayrollController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PayrollController::class, 'update'])->name('update');
            Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/pdf', [PayrollController::class, 'generatePDF'])->name('pdf');
        });
        
        Route::prefix('payroll-slip')->name('payroll.slip.')->group(function () {
            Route::get('/{id}', [PayrollController::class, 'showSlip'])->name('show');
            Route::get('/{id}/pdf', [PayrollController::class, 'downloadSlipPdf'])->name('pdf');
        });

        // Report routes with permission middleware
        Route::prefix('reports')->name('reports.')->middleware('can:view reports')->group(function () {
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/quotations', [ReportController::class, 'quotations'])->name('quotations');
            Route::get('/invoices', [ReportController::class, 'invoices'])->name('invoices');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | API Routes (ภายในระบบ)
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->middleware(['auth'])->group(function () {
        Route::get('/wages', [WageController::class, 'getWageData'])->name('wages');
        Route::get('/check-payroll', [PayrollController::class, 'checkPayroll'])->name('payroll.check');
    });
});

/*
|--------------------------------------------------------------------------
| Testing Routes (เฉพาะเมื่อ APP_ENV=local เท่านั้น)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
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

        $pdfService = new ThaiPdfService();
        return $pdfService->generatePdf($html);
    });

    // เพิ่ม route สำหรับทดสอบการเข้าสู่ระบบ (เฉพาะ development เท่านั้น)
    Route::get('/test-users', [TestLoginController::class, 'showUsers']);
    Route::post('/test-login-attempt', [TestLoginController::class, 'testLogin']);
}

// Test route using controller
Route::get('/test', [TestController::class, 'index']);
