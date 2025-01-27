<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;

// หน้าแรก
Route::get('/', function () {
    return view('welcome'); // หน้าสำหรับผู้เยี่ยมชมทั่วไป
})->name('welcome');

// หน้า Home ถูกเปลี่ยนเส้นทางไปที่ Dashboard (หลังจากเข้าสู่ระบบ)
Route::get('/home', function () {
    return redirect()->route('dashboard'); // เปลี่ยนเส้นทางไปที่ Dashboard
})->middleware('auth')->name('home');

// เส้นทาง Authentication
Auth::routes();

// Route สำหรับ Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Resource Routes ถ้าต้องการให้มีการจำกัดการเข้าถึงด้วยการเข้าสู่ระบบ
Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('orders.order-items', OrderItemController::class);

    // Route ชั่วคราวสำหรับ Profile (ถ้าใช้)
    Route::get('/profile', function () {
        return 'This is the profile page.';
    })->name('profile.show');
});
