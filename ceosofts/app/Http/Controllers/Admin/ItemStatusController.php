<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemStatus;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ItemStatusController extends Controller
{
    /**
     * แสดงรายการสถานะของสินค้า
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', ItemStatus::class);
        
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $query = ItemStatus::query();
        
        // Apply search if provided
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results with product counts
        $cacheKey = "item_statuses_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $statuses = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->withCount('products')->paginate($perPage);
        });
        
        return view('admin.item_statuses.index', compact('statuses', 'search', 'sortField', 'sortDirection'));
    }

    /**
     * แสดงฟอร์มสร้างสถานะใหม่
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', ItemStatus::class);
        
        return view('admin.item_statuses.create');
    }

    /**
     * บันทึกข้อมูลสถานะใหม่
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', ItemStatus::class);
        
        $validated = $request->validate([
            'name' => 'required|unique:item_statuses,name|max:255',
            'description' => 'nullable|max:1000',
            'color' => 'nullable|max:50',
            'is_active' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $status = new ItemStatus();
            $status->fill($validated);
            $status->is_active = $request->has('is_active');
            $status->save();
            
            // Clear item status cache
            $this->clearItemStatusCache();
            
            DB::commit();
            
            Log::info('Item status created', ['id' => $status->id, 'name' => $status->name]);
            
            return redirect()->route('admin.item_statuses.index')
                ->with('success', 'สถานะ "' . $status->name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create item status', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create item status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * แสดงรายละเอียดสถานะและสินค้าที่มีสถานะนี้
     * 
     * @param ItemStatus $itemStatus
     * @return \Illuminate\View\View
     */
    public function show(ItemStatus $itemStatus)
    {
        $this->authorize('view', $itemStatus);
        
        // Load products with this status
        $products = Product::where('status_id', $itemStatus->id)
            ->orderBy('name')
            ->paginate(10);
            
        return view('admin.item_statuses.show', compact('itemStatus', 'products'));
    }

    /**
     * แสดงฟอร์มแก้ไขสถานะ
     * 
     * @param ItemStatus $itemStatus
     * @return \Illuminate\View\View
     */
    public function edit(ItemStatus $itemStatus)
    {
        $this->authorize('update', $itemStatus);
        
        return view('admin.item_statuses.edit', compact('itemStatus'));
    }

    /**
     * อัปเดตข้อมูลสถานะ
     * 
     * @param Request $request
     * @param ItemStatus $itemStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ItemStatus $itemStatus)
    {
        $this->authorize('update', $itemStatus);
        
        $validated = $request->validate([
            'name' => 'required|unique:item_statuses,name,' . $itemStatus->id . '|max:255',
            'description' => 'nullable|max:1000',
            'color' => 'nullable|max:50',
            'is_active' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $previousName = $itemStatus->name;
            
            $itemStatus->fill($validated);
            $itemStatus->is_active = $request->has('is_active');
            
            if ($itemStatus->isDirty()) {
                $itemStatus->save();
                
                Log::info('Item status updated', [
                    'id' => $itemStatus->id,
                    'name' => $itemStatus->name,
                    'changes' => $itemStatus->getChanges()
                ]);
                
                // Clear cache
                $this->clearItemStatusCache();
                
                $message = 'สถานะ "' . $itemStatus->name . '" ถูกอัปเดตเรียบร้อยแล้ว';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูลสถานะ';
            }
            
            DB::commit();
            
            return redirect()->route('admin.item_statuses.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update item status', [
                'id' => $itemStatus->id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update item status', [
                'id' => $itemStatus->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * ลบข้อมูลสถานะ
     * 
     * @param ItemStatus $itemStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ItemStatus $itemStatus)
    {
        $this->authorize('delete', $itemStatus);
        
        DB::beginTransaction();
        
        try {
            $statusName = $itemStatus->name;
            
            // Check if there are products using this status
            $productCount = Product::where('status_id', $itemStatus->id)->count();
            
            if ($productCount > 0) {
                return back()->withErrors([
                    'error' => 'ไม่สามารถลบสถานะนี้ได้ เนื่องจากมีสินค้าที่ใช้สถานะนี้จำนวน ' . $productCount . ' รายการ'
                ]);
            }
            
            $itemStatus->delete();
            
            // Clear cache
            $this->clearItemStatusCache();
            
            DB::commit();
            
            Log::info('Item status deleted', ['id' => $itemStatus->id, 'name' => $statusName]);
            
            return redirect()->route('admin.item_statuses.index')
                ->with('success', 'สถานะ "' . $statusName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete item status', [
                'id' => $itemStatus->id, 
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบสถานะได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear item status cache.
     * 
     * @return void
     */
    protected function clearItemStatusCache(): void
    {
        // Clear cache keys that might contain item status data
        Cache::flush('item_statuses_page_*');
        Cache::forget('all_item_statuses');
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
                
            default:
                return 'เกิดข้อผิดพลาดในฐานข้อมูล (รหัส: ' . $errorCode . ')';
        }
    }
}
