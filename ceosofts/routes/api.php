<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    CustomerController,
    ProductController,
    OrderController,
    QuotationController,
    InvoiceController,
    EmployeeController,
    AttendanceController,
    ReportController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ===== สำหรับ API ที่ไม่ต้องการ Authentication =====
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => config('app.version', '1.0.0'),
        'environment' => app()->environment(),
    ]);
})->name('api.health');

// API Authentication (login/logout)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

// ===== สำหรับ API ที่ต้องการ Authentication =====
Route::middleware('auth:sanctum')->group(function () {
    // User information
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['roles', 'permissions', 'department']);
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard APIs
    Route::prefix('dashboard')->group(function () {
        Route::get('/summary', [ReportController::class, 'dashboardSummary']);
        Route::get('/sales-chart', [ReportController::class, 'salesChart']);
        Route::get('/pending-tasks', [ReportController::class, 'pendingTasks']);
    });

    // Customer APIs
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/search', [CustomerController::class, 'search']);
    Route::get('customers/by-code/{code}', [CustomerController::class, 'showByCode']);
    
    // Product APIs
    Route::apiResource('products', ProductController::class);
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/by-sku/{sku}', [ProductController::class, 'showBySku']);

    // Order APIs
    Route::apiResource('orders', OrderController::class);
    Route::get('orders/{order}/items', [OrderController::class, 'items']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus']);
    
    // Quotation APIs
    Route::apiResource('quotations', QuotationController::class);
    Route::get('quotations/search', [QuotationController::class, 'search']);
    Route::post('quotations/{quotation}/status', [QuotationController::class, 'updateStatus']);
    
    // Invoice APIs
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/search', [InvoiceController::class, 'search']);
    Route::get('invoices/overdue', [InvoiceController::class, 'overdue']);
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid']);
    
    // HR APIs
    Route::apiResource('employees', EmployeeController::class);
    
    // Attendance APIs
    Route::prefix('attendance')->group(function() {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
        Route::get('/my-attendance', [AttendanceController::class, 'myAttendance']);
    });
    
    // Report APIs
    Route::prefix('reports')->group(function() {
        Route::get('/sales', [ReportController::class, 'salesReport']);
        Route::get('/inventory', [ReportController::class, 'inventoryReport']);
        Route::get('/customers', [ReportController::class, 'customersReport']);
        Route::get('/employees', [ReportController::class, 'employeesReport']);
    });
    
    // Reference Data APIs
    Route::prefix('reference-data')->group(function() {
        Route::get('/departments', function() {
            return \App\Models\Department::all();
        });
        
        Route::get('/positions', function() {
            return \App\Models\Position::all();
        });
        
        Route::get('/job-statuses', function() {
            return \App\Models\JobStatus::all();
        });
        
        Route::get('/payment-terms', function() {
            return [
                ['id' => 'cash', 'name' => 'เงินสด'],
                ['id' => 'credit_30', 'name' => 'เครดิต 30 วัน'],
                ['id' => 'credit_45', 'name' => 'เครดิต 45 วัน'],
                ['id' => 'credit_60', 'name' => 'เครดิต 60 วัน'],
            ];
        });
        
        Route::get('/prefixes', function() {
            return \App\Models\Prefix::all();
        });
    });

    // Mobile App Specific APIs
    Route::prefix('mobile')->group(function() {
        Route::get('/config', function() {
            return response()->json([
                'app_version' => '1.0.0',
                'min_supported_version' => '0.9.0',
                'force_update' => false,
                'maintenance_mode' => false
            ]);
        });
    });
});

// Error handler - invalid JSON
Route::fallback(function(){
    return response()->json([
        'success' => false,
        'message' => 'API Route not found.',
        'status_code' => 404
    ], 404);
});
