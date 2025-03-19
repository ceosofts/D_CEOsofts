<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemStatus;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ItemStatusController extends Controller
{
    /**
     * แสดงรายการสถานะสินค้าทั้งหมด
     */
    public function index()
    {
        try {
            // ดึงข้อมูลสถานะสินค้า - เปลี่ยนจากเรียงตามชื่อเป็นเรียงตาม ID
            $itemStatuses = ItemStatus::orderBy('id')->get();
            
            // นับจำนวนสินค้าในระบบ
            $productCount = 0;
            if (Schema::hasTable('products')) {
                $productCount = Product::count();
            }
            
            // ตรวจสอบการเชื่อมโยงกับสินค้าสำหรับแต่ละสถานะ
            foreach ($itemStatuses as $status) {
                $status->products_count = 0;
                if (Schema::hasTable('products') && Schema::hasColumn('products', 'item_status_id')) {
                    $status->products_count = Product::where('item_status_id', $status->id)->count();
                }
            }
            
            // แสดงข้อมูล
            return view('admin.item_statuses.index', compact('itemStatuses', 'productCount'));
            
        } catch (\Exception $e) {
            Log::error('Error loading item statuses data: ' . $e->getMessage());
            // แสดงข้อความแจ้งเตือนให้ผู้ใช้ทราบ
            return view('admin.item_statuses.index', [
                'itemStatuses' => collect([]), 
                'error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage(),
                'debug_info' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * แสดงฟอร์มสร้างสถานะสินค้าใหม่
     */
    public function create()
    {
        return view('admin.item_statuses.create');
    }

    /**
     * บันทึกข้อมูลสถานะสินค้าใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:item_statuses,name',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        // กำหนดค่าเริ่มต้นให้กับ is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;

        try {
            DB::beginTransaction();
            
            $itemStatus = ItemStatus::create($validated);
            
            DB::commit();
            return redirect()->route('admin.item_statuses.index')
                ->with('success', 'เพิ่มสถานะสินค้าสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing item status: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลสถานะสินค้า
     */
    public function edit(ItemStatus $itemStatus)
    {
        try {
            // นับจำนวนสินค้าที่ใช้สถานะนี้
            if (Schema::hasTable('products') && Schema::hasColumn('products', 'status_id')) {
                $itemStatus->products_count = Product::where('status_id', $itemStatus->id)->count();
            } else {
                $itemStatus->products_count = 0;
            }
            
            return view('admin.item_statuses.edit', compact('itemStatus'));
        } catch (\Exception $e) {
            Log::error('Error loading item status for edit: ' . $e->getMessage());
            
            return redirect()->route('admin.item_statuses.index')
                ->with('error', 'ไม่พบข้อมูลสถานะสินค้าที่ต้องการแก้ไข');
        }
    }

    /**
     * อัปเดตข้อมูลสถานะสินค้า
     */
    public function update(Request $request, ItemStatus $itemStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:item_statuses,name,' . $itemStatus->id,
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        // กำหนดค่า is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;

        try {
            DB::beginTransaction();
            
            $itemStatus->update($validated);
            
            DB::commit();
            
            Log::info('Item status updated successfully', [
                'id' => $itemStatus->id, 
                'data' => $validated
            ]);
            
            return redirect()->route('admin.item_statuses.index')
                ->with('success', 'แก้ไขสถานะสินค้าสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating item status: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลสถานะสินค้า
     */
    public function destroy(ItemStatus $itemStatus)
    {
        try {
            // ตรวจสอบว่ามีการใช้งานในสินค้าหรือไม่
            $productCount = 0;
            if (Schema::hasTable('products') && Schema::hasColumn('products', 'item_status_id')) {
                $productCount = Product::where('item_status_id', $itemStatus->id)->count();
            }
            
            if ($productCount > 0) {
                return back()->with('error', "ไม่สามารถลบได้: สถานะนี้กำลังถูกใช้งานโดยสินค้า {$productCount} รายการ");
            }
            
            $itemStatus->delete();
            return redirect()->route('admin.item_statuses.index')
                ->with('success', 'ลบสถานะรายการสินค้าสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error deleting item status: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
