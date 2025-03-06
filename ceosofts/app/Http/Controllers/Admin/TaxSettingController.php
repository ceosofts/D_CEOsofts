<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaxSettingController extends Controller
{
    /**
     * แสดงรายการการตั้งค่าภาษีทั้งหมด
     */
    public function index()
    {
        // ใช้ paginate เพื่อแบ่งหน้าการแสดงผล (เปลี่ยนเป็น all() หากต้องการแสดงทั้งหมด)
        $taxes = TaxSetting::paginate(10);
        return \view('admin.tax.index', compact('taxes'));
    }

    /**
     * แสดงฟอร์มสร้างการตั้งค่าภาษีใหม่
     */
    public function create()
    {
        return \view('admin.tax.create');
    }

    /**
     * บันทึกข้อมูลการตั้งค่าภาษีใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tax_settings,name',
            'rate' => 'required|numeric|min:0',
        ]);

        try {
            $taxSetting = new TaxSetting();
            $taxSetting->fill($validated);
            $taxSetting->save();

            return \redirect()->route('admin.tax.index')
                ->with('success', 'เพิ่มข้อมูลภาษีสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error storing tax setting: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขการตั้งค่าภาษี
     */
    public function edit(TaxSetting $tax)
    {
        return \view('admin.tax.edit', compact('tax'));
    }

    /**
     * อัปเดตข้อมูลการตั้งค่าภาษีในฐานข้อมูล
     */
    public function update(Request $request, TaxSetting $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tax_settings,name,' . $tax->id,
            'rate' => 'required|numeric|min:0',
        ]);

        try {
            $tax->forceFill($validated);
            $tax->save();

            return \redirect()->route('admin.tax.index')
                ->with('success', 'อัปเดตข้อมูลภาษีสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error updating tax setting: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลการตั้งค่าภาษี
     */
    public function destroy(TaxSetting $tax)
    {
        try {
            $tax->delete();
            return \redirect()->route('admin.tax.index')
                ->with('success', 'ลบข้อมูลภาษีสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error deleting tax setting: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * แสดงรายละเอียดการตั้งค่าภาษี (ถ้าจำเป็น)
     */
    public function show(TaxSetting $tax)
    {
        return \view('admin.tax.show', compact('tax'));
    }
}
