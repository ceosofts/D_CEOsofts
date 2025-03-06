<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyHoliday;
use Illuminate\Support\Facades\Log;

class CompanyHolidayController extends Controller
{
    /**
     * กำหนด Middleware สำหรับ Role:
     * - ผู้ใช้ที่มี role: admin หรือ manager สามารถเข้าดู (index)
     * - ฟอร์มสร้าง, บันทึก, แก้ไข, อัปเดต และลบ ให้เฉพาะ admin เท่านั้น
     */
    public function __construct()
    {
        $this->middleware('role:admin,manager')->only(['index']);
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * แสดงรายการวันหยุดของบริษัทสำหรับปีที่เลือก
     *
     * สำหรับ MySQL ใช้ฟังก์ชัน YEAR() ในการกรองข้อมูล
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y')); // ค่าเริ่มต้นเป็นปีปัจจุบัน

        try {
            // ดึงข้อมูลวันหยุด โดยกรองด้วย YEAR(date)
            $holidays = CompanyHoliday::whereRaw("YEAR(`date`) = ?", [$year])
                ->orderBy('date', 'asc')
                ->paginate(20);

            // ดึงรายการปีที่มีวันหยุด (distinct)
            $years = CompanyHoliday::selectRaw("YEAR(`date`) as year")
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            return \view('company_holidays.index', compact('holidays', 'years', 'year'));
        } catch (\Exception $e) {
            Log::error('Error fetching company holidays: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการดึงข้อมูลวันหยุด']);
        }
    }

    /**
     * แสดงฟอร์มสร้างวันหยุดใหม่
     */
    public function create()
    {
        return \view('company_holidays.create');
    }

    /**
     * บันทึกวันหยุดใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:company_holidays,date',
            'name' => 'required|string|max:255'
        ]);

        try {
            CompanyHoliday::create($request->all());
            return \redirect()->route('company-holidays.index')
                ->with('success', 'เพิ่มวันหยุดสำเร็จ!');
        } catch (\Exception $e) {
            Log::error('Error storing company holiday: ' . $e->getMessage(), $request->all());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการเพิ่มวันหยุด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขวันหยุด
     */
    public function edit(CompanyHoliday $companyHoliday)
    {
        return \view('company_holidays.edit', compact('companyHoliday'));
    }

    /**
     * อัปเดตข้อมูลวันหยุดในฐานข้อมูล
     */
    public function update(Request $request, CompanyHoliday $companyHoliday)
    {
        $request->validate([
            'date' => 'required|date|unique:company_holidays,date,' . $companyHoliday->id,
            'name' => 'required|string|max:255'
        ]);

        try {
            $companyHoliday->update($request->all());
            return \redirect()->route('company-holidays.index')
                ->with('success', 'อัปเดตวันหยุดสำเร็จ!');
        } catch (\Exception $e) {
            Log::error('Error updating company holiday: ' . $e->getMessage(), $request->all());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการอัปเดตวันหยุด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบวันหยุดออกจากฐานข้อมูล
     */
    public function destroy(CompanyHoliday $companyHoliday)
    {
        try {
            $companyHoliday->delete();
            return \redirect()->route('company-holidays.index')
                ->with('success', 'ลบวันหยุดสำเร็จ!');
        } catch (\Exception $e) {
            Log::error('Error deleting company holiday: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการลบวันหยุด: ' . $e->getMessage()]);
        }
    }
}
