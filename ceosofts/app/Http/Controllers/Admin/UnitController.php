<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    /**
     * แสดงรายการหน่วยสินค้า
     */
    public function index()
    {
        // ใช้ paginate เพื่อแบ่งหน้าแสดงผล
        $units = Unit::paginate(10);
        return \view('admin.units.index', compact('units'));
    }

    /**
     * แสดงฟอร์มสร้างหน่วยสินค้าใหม่
     */
    public function create()
    {
        return \view('admin.units.create');
    }

    /**
     * บันทึกหน่วยสินค้าใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:units,name|max:255',
        ]);

        try {
            $unit = new Unit();
            $unit->fill($validated);
            $unit->save();

            return \redirect()->route('admin.units.index')
                ->with('success', 'หน่วยสินค้าเพิ่มสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error storing unit: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลหน่วยสินค้า
     */
    public function edit(Unit $unit)
    {
        return \view('admin.units.edit', compact('unit'));
    }

    /**
     * อัปเดตข้อมูลหน่วยสินค้าในฐานข้อมูล
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|unique:units,name,' . $unit->id . '|max:255',
        ]);

        try {
            $unit->forceFill($validated);
            $unit->save();

            return \redirect()->route('admin.units.index')
                ->with('success', 'อัปเดตหน่วยสินค้าสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error updating unit: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลหน่วยสินค้า
     */
    public function destroy(Unit $unit)
    {
        try {
            $unit->delete();
            return \redirect()->route('admin.units.index')
                ->with('success', 'ลบหน่วยสินค้าสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error deleting unit: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * แสดงรายละเอียดหน่วยสินค้า (ถ้าจำเป็น)
     */
    public function show(Unit $unit)
    {
        return \view('admin.units.show', compact('unit'));
    }
}
