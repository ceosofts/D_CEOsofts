<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemStatus;
use Illuminate\Support\Facades\Log;

class ItemStatusController extends Controller
{
    /**
     * แสดงรายการสถานะของสินค้า
     */
    public function index()
    {
        // ใช้ paginate เพื่อแบ่งหน้าการแสดงผล (สามารถใช้ all() หากต้องการแสดงทั้งหมด)
        $statuses = ItemStatus::paginate(10);
        return \view('admin.item_statuses.index', compact('statuses'));
    }

    /**
     * แสดงฟอร์มสร้างสถานะใหม่
     */
    public function create()
    {
        return \view('admin.item_statuses.create');
    }

    /**
     * บันทึกข้อมูลสถานะใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:item_statuses,name|max:255',
        ]);

        try {
            $status = new ItemStatus();
            $status->fill($validated);
            $status->save();

            return \redirect()->route('admin.item_statuses.index')
                ->with('success', 'เพิ่มสถานะสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error storing item status: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขสถานะ
     */
    public function edit(ItemStatus $itemStatus)
    {
        return \view('admin.item_statuses.edit', compact('itemStatus'));
    }

    /**
     * อัปเดตข้อมูลสถานะ
     */
    public function update(Request $request, ItemStatus $itemStatus)
    {
        $validated = $request->validate([
            'name' => 'required|unique:item_statuses,name,' . $itemStatus->id . '|max:255',
        ]);

        try {
            $itemStatus->forceFill($validated);
            $itemStatus->save();

            return \redirect()->route('admin.item_statuses.index')
                ->with('success', 'อัปเดตสถานะสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error updating item status: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลสถานะ
     */
    public function destroy(ItemStatus $itemStatus)
    {
        try {
            $itemStatus->delete();
            return \redirect()->route('admin.item_statuses.index')
                ->with('success', 'ลบสถานะสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error deleting item status: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
