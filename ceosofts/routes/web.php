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

// 🏠 Welcome Page
Route::get('/', fn() => view('welcome'))->name('welcome');

// 🏠 Redirect Home → Dashboard (requires authentication)
Route::get('/home', fn() => redirect()->route('dashboard'))
    ->middleware('auth')
    ->name('home');

// 🔐 Authentication Routes
Auth::routes();

// 🏠 Dashboard (requires auth and department middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'department'])
    ->name('dashboard');

// 🌟 Admin Routes (เฉพาะ Admin เท่านั้น)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // หน้าแรกของ Admin
    Route::get('/', fn() => "Welcome Admin");

    // จัดการบริษัท
    Route::resource('companies', CompanyController::class);

    // จัดการผู้ใช้
    Route::resource('users', UserController::class);

    // จัดการแผนก, หน่วยนับ, ตำแหน่ง, คำนำหน้าชื่อ
    Route::resources([
        'departments' => DepartmentController::class,
        'units'       => UnitController::class,
        'positions'   => PositionController::class,
        'prefixes'    => PrefixController::class,
    ]);

    // จัดการสถานะของสินค้า, การชำระเงิน, การตั้งค่าภาษี
    Route::resources([
        'item_statuses'    => ItemStatusController::class,
        'payment_statuses' => PaymentStatusController::class,
        'tax'              => TaxSettingController::class,
    ]);
});

// 📦 Resource Routes (ต้อง Login และอยู่ในแผนก)
Route::middleware(['auth', 'department'])->group(function () {

    // Customers, Products, Orders
    Route::resources([
        'customers' => CustomerController::class,
        'products'  => ProductController::class,
        'orders'    => OrderController::class,
    ]);

    // Order Items (Nested Resource)
    Route::resource('orders.order-items', OrderItemController::class);

    // จัดการพนักงาน & วันหยุดของบริษัท
    Route::resources([
        'employees'        => EmployeeController::class,
        'company-holidays' => CompanyHolidayController::class,
    ]);

    // จัดการเวลาทำงาน (เฉพาะ Admin สำหรับ attendance)
    Route::resource('attendances', AttendanceController::class)
        ->middleware('role:admin');

    // จัดการเวลาทำงาน (Work Shifts)
    Route::resource('work-shifts', WorkShiftController::class);

    // จัดการค่าจ้าง (Wages)
    // Route::get('/wages-summary', [WageController::class, 'index'])->name('wages.summary');
    // Route::get('/api/wages', [WageController::class, 'getWageData'])->name('api.wages');
    // Route::post('/store-monthly-wages', [WageController::class, 'storeMonthlyWages'])->name('wages.store');
    // Route::post('/wages/store-monthly', [WageController::class, 'storeMonthlyWages'])->name('wages.storeMonthly');

    // สรุปค่าแรง
    Route::get('/wages-summary', [WageController::class, 'index'])->name('wages.summary');
    // บันทึกค่าแรง
    Route::post('/wages/store-monthly', [WageController::class, 'storeMonthlyWages'])->name('wages.storeMonthly');
    // API ให้หน้า create payroll slip เรียก
    Route::get('/api/wages', [WageController::class, 'getWageData'])->name('api.wages');



    // **Payroll Routes**
    // ภายใน Route::middleware(['auth', 'department'])->group(function () { ... }

    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payrolls/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('/payrolls', [PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/payrolls/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
    Route::put('/payrolls/{id}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::delete('/payrolls/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    Route::get('/payroll-slip/{id}', [PayrollController::class, 'showSlip'])->name('payroll.slip');
    Route::get('/payroll-slip/{id}/pdf', [PayrollController::class, 'downloadSlipPdf'])->name('payroll.slip.pdf');
    Route::get('/api/check-payroll', [PayrollController::class, 'checkPayroll'])->name('payroll.check');

    // ค้นหา Customer ตาม Code
    Route::prefix('customers/code/{code}')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'showByCode'])->name('showByCode');
        Route::get('/edit', [CustomerController::class, 'editByCode'])->name('editByCode');
        Route::put('/', [CustomerController::class, 'updateByCode'])->name('updateByCode');
        Route::delete('/', [CustomerController::class, 'destroyByCode'])->name('destroyByCode');
    });

    // Profile Page
    Route::get('/profile', fn() => 'This is the profile page.')->name('profile.show');
});
