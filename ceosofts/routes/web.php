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
    QuotationController
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
use Barryvdh\DomPDF\Facade\Pdf; // Add this line
use App\Services\ThaiPdfService;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('welcome');

// Authentication Routes
Auth::routes();

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

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Redirect /home to dashboard
    Route::get('/home', fn() => redirect()->route('dashboard'))->name('home');

    // Dashboard (requires department middleware)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('department')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (สำหรับผู้ใช้ที่มี role: admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Home
        Route::get('/', fn() => "Welcome Admin");

        // Manage Companies
        Route::resource('companies', CompanyController::class);

        // Manage Users
        Route::resource('users', UserController::class);

        // Manage Departments, Units, Positions, Prefixes
        Route::resources([
            'departments' => DepartmentController::class,
            'units'       => UnitController::class,
            'positions'   => PositionController::class,
            'prefixes'    => PrefixController::class,
        ]);

        // Manage Item Statuses, Payment Statuses, and Tax Settings
        Route::resources([
            'item_statuses'    => ItemStatusController::class,
            'payment_statuses' => PaymentStatusController::class,
            'tax'              => TaxSettingController::class,
        ]);
    });

    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
        Route::resource('job-statuses', JobStatusController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Resource Routes (สำหรับผู้ใช้ที่มี department)
    |--------------------------------------------------------------------------
    */
    Route::middleware('department')->group(function () {

        // Customers, Products, and Orders
        Route::resources([
            'customers' => CustomerController::class,
            'products'  => ProductController::class,
            'orders'    => OrderController::class,
        ]);

        // Nested Resource: Order Items for a given Order
        Route::resource('orders.order-items', OrderItemController::class);

        // Manage Employees and Company Holidays
        Route::resources([
            'employees'        => EmployeeController::class,
            'company-holidays' => CompanyHolidayController::class,
        ]);

        // Attendance Routes (เฉพาะ admin)
        Route::resource('attendances', AttendanceController::class)
            ->middleware('role:admin');

        // Quotations – สามารถใช้ Route::resource ได้โดยตรง
        Route::resource('quotations', QuotationController::class);
        Route::get('quotations/{quotation}/export', [QuotationController::class, 'export'])->name('quotations.export');

        // Work Shifts
        Route::resource('work-shifts', WorkShiftController::class);

        // Wages & Payroll
        Route::get('/wages-summary', [WageController::class, 'index'])->name('wages.summary');
        Route::post('/wages/store-monthly', [WageController::class, 'storeMonthlyWages'])->name('wages.storeMonthly');
        Route::get('/api/wages', [WageController::class, 'getWageData'])->name('api.wages');

        Route::get('/payrolls', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payrolls/create', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/payrolls', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payrolls/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
        Route::put('/payrolls/{id}', [PayrollController::class, 'update'])->name('payroll.update');
        Route::delete('/payrolls/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
        Route::get('/payroll-slip/{id}', [PayrollController::class, 'showSlip'])->name('payroll.slip');
        Route::get('/payroll-slip/{id}/pdf', [PayrollController::class, 'downloadSlipPdf'])->name('payroll.slip.pdf');
        Route::get('/api/check-payroll', [PayrollController::class, 'checkPayroll'])->name('payroll.check');
        Route::get('/payrolls/{id}/pdf', [PayrollController::class, 'generatePDF'])->name('payroll.pdf');

        // Customer lookup by code (custom routes)
        Route::prefix('customers/code/{code}')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'showByCode'])->name('showByCode');
            Route::get('/edit', [CustomerController::class, 'editByCode'])->name('editByCode');
            Route::put('/', [CustomerController::class, 'updateByCode'])->name('updateByCode');
            Route::delete('/', [CustomerController::class, 'destroyByCode'])->name('destroyByCode');
        });

        // Profile Page
        Route::get('/profile', fn() => 'This is the profile page.')->name('profile.show');
    });
});
