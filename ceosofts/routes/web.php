<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Admin\UserController; // ✅ เพิ่ม Controller สำหรับ Admin

// 🏠 **หน้าแรก (Welcome Page)**
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 🏠 **Redirect Home → Dashboard (ต้อง Login)**
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');

// 🔐 **Authentication Routes**
Auth::routes();

// 🏠 **Dashboard (ต้อง Login และอยู่ในแผนก)**
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'department'])
    ->name('dashboard');

// 🌟 **Admin Routes (เฉพาะ Admin เท่านั้น)**
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
//     Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {    
//     Route::resource('departments', DepartmentController::class);
//     Route::resource('users', UserController::class); // ✅ เพิ่ม Route สำหรับ Admin จัดการ Users
// });

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {    
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class); // ✅ ใช้ name('admin.') ทำให้ route('admin.users.index') ใช้งานได้
});



// 👥 **Manager, Leader, User (ต้องมี Role และอยู่ในแผนก)**
Route::middleware(['auth', 'role:manager,leader,user', 'department'])->group(function () {
    Route::get('/departments/{department_id}', [DepartmentController::class, 'show'])->name('departments.show');
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
    Route::get('/profile', function () {
        return 'This is the profile page.';
    })->name('profile.show');
});
