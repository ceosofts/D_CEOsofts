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
    WageController
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
    TaxSettingController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Welcome Page
Route::get('/', fn() => view('welcome'))->name('welcome');

// Authentication Routes
Auth::routes();

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Redirect /home to dashboard (requires authentication)
Route::get('/home', fn() => redirect()->route('dashboard'))
    ->middleware('auth')
    ->name('home');

// Dashboard (requires auth and department middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'department'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes (For users with 'admin' role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Home
    Route::get('/', fn() => "Welcome Admin");

    // Manage Companies
    Route::resource('companies', CompanyController::class);

    // Manage Users
    Route::resource('users', UserController::class);

    // Manage Departments, Units, Positions, and Prefixes
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

/*
|--------------------------------------------------------------------------
| Resource Routes (For authenticated users with department)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'department'])->group(function () {

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

    // Attendance Routes (restricted to admin role)
    Route::resource('attendances', AttendanceController::class)
        ->middleware('role:admin');

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
