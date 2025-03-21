<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStatusRequest;
use App\Models\PaymentStatus;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class PaymentStatusController extends Controller
{
    /**
     * Cache TTL in minutes
     */
    protected const CACHE_TTL = 10;

    /**
     * Display a listing of payment statuses.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', PaymentStatus::class);

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $filter = $request->get('filter');

        $query = PaymentStatus::query();
        
        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply filters
        if ($filter) {
            switch ($filter) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'default':
                    $query->where('is_default', true);
                    break;
            }
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Cache key based on query parameters
        $cacheKey = "payment_statuses_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}_{$filter}";
        
        // Get paginated results, caching frequently accessed pages
        $paymentStatuses = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function() use ($query, $perPage) {
            return $query->withCount('invoices')->paginate($perPage);
        });
        
        // Get additional stats for display
        $stats = $this->getPaymentStatusStats();
        
        return view('admin.payment_statuses.index', compact(
            'paymentStatuses',
            'search',
            'sortField',
            'sortDirection',
            'filter',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new payment status.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', PaymentStatus::class);
        
        // Check if we already have a default status
        $hasDefaultStatus = Cache::remember('has_default_payment_status', now()->addMinutes(60), function() {
            return PaymentStatus::where('is_default', true)->exists();
        });
        
        return view('admin.payment_statuses.create', compact('hasDefaultStatus'));
    }

    /**
     * Store a newly created payment status in storage.
     *
     * @param  \App\Http\Requests\PaymentStatusRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PaymentStatusRequest $request)
    {
        $this->authorize('create', PaymentStatus::class);
        
        // Validation is handled by PaymentStatusRequest
        $validated = $request->validated();
        
        // Set default values for boolean fields if not provided
        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');
        
        DB::beginTransaction();
        
        try {
            // If this is set as default, unset all other defaults
            if ($validated['is_default']) {
                PaymentStatus::where('is_default', 1)->update(['is_default' => 0]);
            }
            
            $paymentStatus = PaymentStatus::create($validated);
            
            // Add audit log entry
            activity()
                ->performedOn($paymentStatus)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $validated])
                ->log('created_payment_status');
            
            // Clear cache related to payment statuses
            $this->clearPaymentStatusCache();
            
            DB::commit();
            
            Log::info('Payment status created', [
                'id' => $paymentStatus->id, 
                'name' => $paymentStatus->name,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('admin.payment-statuses.index')
                ->with('success', 'สถานะการชำระเงิน "' . $paymentStatus->name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create payment status', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'data' => $validated,
                'user_id' => Auth::id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create payment status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * Display the specified payment status.
     *
     * @param  \App\Models\PaymentStatus  $paymentStatus
     * @return \Illuminate\View\View
     */
    public function show(PaymentStatus $paymentStatus)
    {
        $this->authorize('view', $paymentStatus);
        
        // Cache key for invoice counts
        $cacheKey = "payment_status_{$paymentStatus->id}_stats";
        
        // Get invoice statistics for this payment status
        $stats = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function() use ($paymentStatus) {
            $invoices = Invoice::where('payment_status_id', $paymentStatus->id);
            
            return [
                'total' => $invoices->count(),
                'total_amount' => $invoices->sum('total_amount'),
                'last_month' => $invoices->where('created_at', '>=', now()->subMonth())->count(),
                'last_week' => $invoices->where('created_at', '>=', now()->subWeek())->count(),
            ];
        });
        
        // Load related invoices with pagination
        $invoices = Invoice::with(['customer'])
            ->where('payment_status_id', $paymentStatus->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.payment_statuses.show', compact('paymentStatus', 'invoices', 'stats'));
    }

    /**
     * Show the form for editing the specified payment status.
     *
     * @param  \App\Models\PaymentStatus  $paymentStatus
     * @return \Illuminate\View\View
     */
    public function edit(PaymentStatus $paymentStatus)
    {
        $this->authorize('update', $paymentStatus);
        
        // Get invoice count for this status to show warning if it's in use
        $invoiceCount = Cache::remember("payment_status_{$paymentStatus->id}_invoice_count", now()->addMinutes(self::CACHE_TTL), function() use ($paymentStatus) {
            return Invoice::where('payment_status_id', $paymentStatus->id)->count();
        });
        
        return view('admin.payment_statuses.edit', compact('paymentStatus', 'invoiceCount'));
    }

    /**
     * Update the specified payment status in storage.
     *
     * @param  \App\Http\Requests\PaymentStatusRequest  $request
     * @param  \App\Models\PaymentStatus  $paymentStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PaymentStatusRequest $request, PaymentStatus $paymentStatus)
    {
        $this->authorize('update', $paymentStatus);
        
        // Validation is handled by PaymentStatusRequest
        $validated = $request->validated();
        
        // Set default values for boolean fields
        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');
        
        DB::beginTransaction();
        
        try {
            // Save original values for logging changes
            $originalValues = $paymentStatus->getAttributes();
            
            // If this is set as default, unset all other defaults
            if ($validated['is_default'] && !$paymentStatus->is_default) {
                PaymentStatus::where('is_default', 1)->update(['is_default' => 0]);
            }
            
            $paymentStatus->update($validated);
            
            // Check if there were actual changes
            if ($paymentStatus->wasChanged()) {
                // Log the specific changes
                $changes = [];
                foreach ($paymentStatus->getChanges() as $field => $newValue) {
                    if ($field !== 'updated_at') {
                        $changes[$field] = [
                            'from' => $originalValues[$field] ?? null,
                            'to' => $newValue
                        ];
                    }
                }
                
                // Add audit log entry
                activity()
                    ->performedOn($paymentStatus)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => array_intersect_key($originalValues, $changes),
                        'attributes' => array_intersect_key($paymentStatus->getAttributes(), $changes),
                        'changes' => $changes
                    ])
                    ->log('updated_payment_status');
                
                Log::info('Payment status updated', [
                    'id' => $paymentStatus->id,
                    'name' => $paymentStatus->name,
                    'changes' => $changes,
                    'user_id' => Auth::id()
                ]);
                
                // Clear cache related to payment statuses
                $this->clearPaymentStatusCache();
                
                $message = 'สถานะการชำระเงิน "' . $paymentStatus->name . '" ถูกอัปเดตเรียบร้อยแล้ว';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูล';
            }
            
            DB::commit();
            
            return redirect()->route('admin.payment-statuses.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update payment status', [
                'id' => $paymentStatus->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'data' => $validated,
                'user_id' => Auth::id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update payment status', [
                'id' => $paymentStatus->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * Remove the specified payment status from storage.
     *
     * @param  \App\Models\PaymentStatus  $paymentStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PaymentStatus $paymentStatus)
    {
        $this->authorize('delete', $paymentStatus);
        
        DB::beginTransaction();
        
        try {
            $statusName = $paymentStatus->name;
            
            // Check if status is in use by any invoices
            $invoiceCount = Invoice::where('payment_status_id', $paymentStatus->id)->count();
            
            // Check if this is the default status
            if ($paymentStatus->is_default) {
                return back()->withErrors([
                    'error' => "ไม่สามารถลบสถานะ '{$statusName}' ได้ เนื่องจากเป็นสถานะเริ่มต้นของระบบ"
                ]);
            }
            
            if ($invoiceCount > 0) {
                return back()->withErrors([
                    'error' => "ไม่สามารถลบสถานะ '{$statusName}' ได้ เนื่องจากมีใบเสร็จ/ใบแจ้งหนี้ {$invoiceCount} รายการที่ใช้สถานะนี้อยู่"
                ]);
            }
            
            // Add audit log entry before deletion
            activity()
                ->performedOn($paymentStatus)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $paymentStatus->getAttributes()])
                ->log('deleted_payment_status');
                
            $paymentStatus->delete();
            
            // Clear cache related to payment statuses
            $this->clearPaymentStatusCache();
            
            DB::commit();
            
            Log::info('Payment status deleted', [
                'id' => $paymentStatus->id,
                'name' => $statusName,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('admin.payment-statuses.index')
                ->with('success', 'สถานะการชำระเงิน "' . $statusName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete payment status', [
                'id' => $paymentStatus->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบสถานะการชำระเงินได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Toggle active status of the specified payment status.
     *
     * @param  \App\Models\PaymentStatus  $paymentStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(PaymentStatus $paymentStatus)
    {
        $this->authorize('update', $paymentStatus);
        
        DB::beginTransaction();
        
        try {
            $originalStatus = $paymentStatus->is_active;
            $paymentStatus->is_active = !$originalStatus;
            $paymentStatus->save();
            
            // Clear cache related to payment statuses
            $this->clearPaymentStatusCache();
            
            // Add audit log entry
            activity()
                ->performedOn($paymentStatus)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => ['is_active' => $originalStatus],
                    'attributes' => ['is_active' => $paymentStatus->is_active]
                ])
                ->log('toggled_payment_status_active');
            
            DB::commit();
            
            $status = $paymentStatus->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            return back()->with('success', "สถานะ '{$paymentStatus->name}' ถูก{$status}แล้ว");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle payment status active state', [
                'id' => $paymentStatus->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return back()->withErrors(['error' => 'ไม่สามารถเปลี่ยนสถานะการใช้งานได้']);
        }
    }
    
    /**
     * Process bulk actions on payment statuses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $this->authorize('update-any', PaymentStatus::class);
        
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:payment_statuses,id'
        ]);
        
        $action = $request->input('action');
        $ids = $request->input('ids');
        
        DB::beginTransaction();
        
        try {
            $paymentStatuses = PaymentStatus::whereIn('id', $ids)->get();
            
            // Make sure we don't delete or deactivate default statuses
            if (in_array($action, ['delete', 'deactivate'])) {
                $defaultStatus = $paymentStatuses->where('is_default', true)->first();
                if ($defaultStatus) {
                    return back()->withErrors([
                        'error' => "ไม่สามารถ " . ($action == 'delete' ? 'ลบ' : 'ปิดใช้งาน') . 
                                  " สถานะ '{$defaultStatus->name}' ได้ เนื่องจากเป็นสถานะเริ่มต้นของระบบ"
                    ]);
                }
            }
            
            // Make sure we don't delete statuses that are in use
            if ($action === 'delete') {
                $inUseStatuses = new Collection();
                
                foreach ($paymentStatuses as $status) {
                    $invoiceCount = Invoice::where('payment_status_id', $status->id)->count();
                    if ($invoiceCount > 0) {
                        $inUseStatuses->push((object)[
                            'name' => $status->name,
                            'count' => $invoiceCount
                        ]);
                    }
                }
                
                if ($inUseStatuses->isNotEmpty()) {
                    $errorMessage = "ไม่สามารถลบสถานะบางรายการได้ เนื่องจากมีการใช้งานอยู่:<br>";
                    foreach ($inUseStatuses as $status) {
                        $errorMessage .= "- {$status->name}: {$status->count} รายการ<br>";
                    }
                    
                    return back()->withErrors(['error' => $errorMessage]);
                }
                
                // Delete the statuses if they're not in use
                foreach ($paymentStatuses as $status) {
                    activity()
                        ->performedOn($status)
                        ->causedBy(Auth::user())
                        ->withProperties(['attributes' => $status->getAttributes()])
                        ->log('bulk_deleted_payment_status');
                    
                    $status->delete();
                }
                
                $message = 'ลบสถานะการชำระเงินจำนวน ' . count($ids) . ' รายการเรียบร้อยแล้ว';
            } else {
                // Activate or deactivate
                $value = ($action === 'activate');
                $updatedCount = PaymentStatus::whereIn('id', $ids)->update(['is_active' => $value]);
                
                // Log the action
                activity()
                    ->causedBy(Auth::user())
                    ->withProperties(['ids' => $ids, 'action' => $action])
                    ->log('bulk_' . $action . '_payment_statuses');
                
                $actionText = $value ? 'เปิด' : 'ปิด';
                $message = "{$actionText}ใช้งานสถานะการชำระเงินจำนวน {$updatedCount} รายการเรียบร้อยแล้ว";
            }
            
            // Clear cache
            $this->clearPaymentStatusCache();
            
            DB::commit();
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process bulk action on payment statuses', [
                'action' => $action,
                'ids' => $ids,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Get payment status statistics for the dashboard.
     * 
     * @return array
     */
    protected function getPaymentStatusStats(): array
    {
        return Cache::remember('payment_status_summary_stats', now()->addMinutes(self::CACHE_TTL), function() {
            $totalStatuses = PaymentStatus::count();
            $activeStatuses = PaymentStatus::where('is_active', true)->count();
            $defaultStatus = PaymentStatus::where('is_default', true)->first();
            
            // Get most used payment status
            $mostUsedQuery = DB::table('payment_statuses')
                ->leftJoin('invoices', 'payment_statuses.id', '=', 'invoices.payment_status_id')
                ->select('payment_statuses.id', 'payment_statuses.name', DB::raw('COUNT(invoices.id) as count'))
                ->groupBy('payment_statuses.id', 'payment_statuses.name')
                ->orderByDesc('count')
                ->limit(1);
            
            $mostUsed = $mostUsedQuery->first();
            
            return [
                'total' => $totalStatuses,
                'active' => $activeStatuses,
                'default' => $defaultStatus ? $defaultStatus->name : 'None',
                'most_used' => $mostUsed ? [
                    'name' => $mostUsed->name,
                    'count' => $mostUsed->count
                ] : null
            ];
        });
    }
    
    /**
     * Clear payment status cache.
     * 
     * @return void
     */
    protected function clearPaymentStatusCache(): void
    {
        Cache::flush('payment_statuses_page_*');
        Cache::forget('all_payment_statuses');
        Cache::forget('active_payment_statuses');
        Cache::forget('default_payment_status');
        Cache::forget('has_default_payment_status');
        Cache::forget('payment_status_summary_stats');
        
        // Clear individual status caches
        $paymentStatusIds = PaymentStatus::pluck('id')->toArray();
        foreach ($paymentStatusIds as $id) {
            Cache::forget("payment_status_{$id}_stats");
            Cache::forget("payment_status_{$id}_invoice_count");
        }
    }
    
    /**
     * Get a user-friendly database error message.
     * 
     * @param \Illuminate\Database\QueryException $exception
     * @return string
     */
    protected function getDatabaseErrorMessage(QueryException $exception): string
    {
        $errorCode = $exception->getCode();
        
        switch ($errorCode) {
            case '23000': // Integrity constraint violation
                if (strpos($exception->getMessage(), 'Duplicate entry') !== false) {
                    return 'ข้อมูลนี้มีอยู่ในระบบแล้ว กรุณาตรวจสอบข้อมูลซ้ำ';
                }
                return 'ข้อมูลขัดแย้งกับข้อมูลอื่นในระบบ';
                
            case '22001': // String data right truncation
                return 'ข้อมูลที่กรอกมีความยาวเกินกว่าที่กำหนด';
                
            case '42S22': // Column not found
                return 'พบปัญหาในการเข้าถึงฐานข้อมูล: คอลัมน์ไม่ถูกต้อง';
                
            case '42S02': // Table not found
                return 'พบปัญหาในการเข้าถึงฐานข้อมูล: ไม่พบตาราง';
                
            default:
                return 'เกิดข้อผิดพลาดในฐานข้อมูล (รหัส: ' . $errorCode . ')';
        }
    }
    
    /**
     * API endpoints for payment statuses (used by front-end JavaScript)
     */
    
    /**
     * Get all active payment statuses as JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveStatusesJson()
    {
        $statuses = Cache::remember('active_payment_statuses_json', now()->addMinutes(30), function() {
            return PaymentStatus::where('is_active', true)
                ->orderBy('name')
                ->select('id', 'name', 'color', 'is_default')
                ->get();
        });
        
        return response()->json($statuses);
    }
    
    /**
     * Search payment statuses by name.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $limit = $request->get('limit', 10);
        
        $statuses = PaymentStatus::where('name', 'LIKE', "%{$query}%")
            ->where('is_active', true)
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'color']);
            
        return response()->json($statuses);
    }
}
