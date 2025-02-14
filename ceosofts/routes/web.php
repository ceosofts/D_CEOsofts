<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    DashboardController, CustomerController, ProductController, OrderController, 
    OrderItemController, EmployeeController, CompanyHolidayController, 
    AttendanceController, WorkShiftController, PayrollController, WageController
};
use App\Http\Controllers\Admin\{
    UserController, DepartmentController, CompanyController, UnitController, 
    PositionController, PrefixController, ItemStatusController, 
    PaymentStatusController, TaxSettingController
};

// 🏠 **หน้าแรก (Welcome Page)**
Route::get('/', fn() => view('welcome'))->name('welcome');

// 🏠 **Redirect Home → Dashboard (ต้อง Login)**
Route::get('/home', fn() => redirect()->route('dashboard'))->middleware('auth')->name('home');

// 🔐 **Authentication Routes**
Auth::routes();

// 🏠 **Dashboard (ต้อง Login และอยู่ในแผนก)**
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'department'])
    ->name('dashboard');

// 🌟 **Admin Routes (เฉพาะ Admin เท่านั้น)**
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => "Welcome Admin");

    // 🏢 **จัดการบริษัท**
    Route::resource('companies', CompanyController::class);
    
    // 👥 **จัดการผู้ใช้**
    Route::resource('users', UserController::class);

    // 🏢 **จัดการแผนก, หน่วยนับ, ตำแหน่ง, คำนำหน้าชื่อ**
    Route::resources([
        'departments' => DepartmentController::class,
        'units' => UnitController::class,
        'positions' => PositionController::class,
        'prefixes' => PrefixController::class,
    ]);

    // 📦 **จัดการสถานะของสินค้า, การชำระเงิน, การตั้งค่าภาษี**
    Route::resources([
        'item_statuses' => ItemStatusController::class,
        'payment_statuses' => PaymentStatusController::class,
        'tax' => TaxSettingController::class,
    ]);
});

// 📦 **Resource Routes (ต้อง Login และอยู่ในแผนก)**
Route::middleware(['auth', 'department'])->group(function () {

    // 📦 **Customers, Products, Orders**
    Route::resources([
        'customers' => CustomerController::class,
        'products' => ProductController::class,
        'orders' => OrderController::class,
    ]);

    // 📦 **Order Items (แบบ Nested)**
    Route::resource('orders.order-items', OrderItemController::class);

    // 🏢 **จัดการพนักงาน & วันหยุดของบริษัท**
    Route::resources([
        'employees' => EmployeeController::class,
        'company-holidays' => CompanyHolidayController::class,
    ]);

    // 🏢 **จัดการเวลาทำงาน**
    Route::resource('attendances', AttendanceController::class)
        ->middleware('role:admin'); // ✅ ใช้ Middleware เฉพาะ Admin


    // 🏢 **จัดการเงินเดือน**
    // Route::get('/payroll-summary', [PayrollController::class, 'index'])->name('payroll.summary');


    // 🏢 **จัดการค่าจ้าง**
    Route::get('/wages-summary', [WageController::class, 'index'])->name('wages.summary');
    
    
    // 🏢 **จัดการเวลาทำงาน**
    Route::resource('work-shifts', WorkShiftController::class);

    // 🏢 **จัดการเงินเดือน**
    Route::post('/store-monthly-wages', [WageController::class, 'storeMonthlyWages'])->name('wages.store');

    // 🏢 **จัดการเงินเดือน**
    Route::post('/wages/store-monthly', [WageController::class, 'storeMonthlyWages'])->name('wages.storeMonthly');



    // 🆔 **ค้นหา Customer ตาม Code**
    Route::prefix('customers/code/{code}')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'showByCode'])->name('showByCode');
        Route::get('/edit', [CustomerController::class, 'editByCode'])->name('editByCode');
        Route::put('/', [CustomerController::class, 'updateByCode'])->name('updateByCode');
        Route::delete('/', [CustomerController::class, 'destroyByCode'])->name('destroyByCode');
    });

    // 👤 **Profile Page**
    Route::get('/profile', fn() => 'This is the profile page.')->name('profile.show');
});
