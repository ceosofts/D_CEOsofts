<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\CompanyController;

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
    Route::get('/', fn() => "Welcome Admin"); // ✅ Route '/admin'
    
    // 🏢 **จัดการบริษัท**
    Route::resource('companies', CompanyController::class);
    
    // 👥 **จัดการผู้ใช้**
    Route::resource('users', UserController::class);

    // 🏢 **จัดการแผนก**
    Route::resource('departments', DepartmentController::class);
});

// 📦 **Resource Routes (ต้อง Login และอยู่ในแผนก)**
Route::middleware(['auth', 'department'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('orders.order-items', OrderItemController::class);

    // 🆔 **ค้นหา Customer ตาม Code**
    Route::prefix('customers/code/{code}')->group(function () {
        Route::get('/', [CustomerController::class, 'showByCode'])->name('customers.showByCode');
        Route::get('/edit', [CustomerController::class, 'editByCode'])->name('customers.editByCode');
        Route::put('/', [CustomerController::class, 'updateByCode'])->name('customers.updateByCode');
        Route::delete('/', [CustomerController::class, 'destroyByCode'])->name('customers.destroyByCode');
    });

    // 👤 **Profile Page (ถ้ามี)**
    Route::get('/profile', fn() => 'This is the profile page.')->name('profile.show');
});
