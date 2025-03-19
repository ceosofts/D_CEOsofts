<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prefix;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrefixController extends Controller
{
    /**
     * แสดงรายการคำนำหน้าชื่อทั้งหมด
     */
    public function index()
    {
        try {
            // ตรวจสอบการเชื่อมต่อฐานข้อมูล
            DB::connection()->getPdo();
            Log::info('Database connected: ' . DB::connection()->getDatabaseName());

            // ทดสอบว่ามีตาราง prefixes หรือไม่
            $hasTable = Schema::hasTable('prefixes');
            if (!$hasTable) {
                Log::error("Table 'prefixes' does not exist in the database");
                return view('admin.prefixes.index', [
                    'prefixes' => collect([]),
                    'error' => 'ไม่พบตาราง prefixes ในฐานข้อมูล กรุณารัน Migration ก่อน'
                ]);
            }

            // ดึงข้อมูลคำนำหน้าชื่อ
            $prefixes = Prefix::orderBy('prefix_th')->get();
            Log::info('Prefixes data loaded: ' . $prefixes->count() . ' records');
            
            // นับจำนวนพนักงานในระบบ
            $employeeCount = 0;
            if (Schema::hasTable('employees')) {
                $employeeCount = Employee::count();
            }
            
            // แสดงข้อมูล
            return view('admin.prefixes.index', compact('prefixes', 'employeeCount'));
            
        } catch (\Exception $e) {
            Log::error('Error loading prefixes data: ' . $e->getMessage());
            // แสดงข้อความแจ้งเตือนให้ผู้ใช้ทราบ
            return view('admin.prefixes.index', [
                'prefixes' => collect([]), 
                'error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage(),
                'debug_info' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * แสดงฟอร์มสร้างคำนำหน้าชื่อใหม่
     */
    public function create()
    {
        return view('admin.prefixes.create');
    }

    /**
     * บันทึกข้อมูลคำนำหน้าชื่อใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefix_th' => 'required|string|max:50|unique:prefixes,prefix_th',
            'prefix_en' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // กำหนดค่าเริ่มต้นให้กับ is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;
        // กำหนดค่า name เท่ากับ prefix_th (สำหรับโครงสร้างตารางเก่า)
        $validated['name'] = $validated['prefix_th'];

        try {
            DB::beginTransaction();
            
            $prefix = Prefix::create($validated);
            
            DB::commit();
            return redirect()->route('admin.prefixes.index')
                ->with('success', 'เพิ่มคำนำหน้าชื่อสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing prefix: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลคำนำหน้าชื่อ
     */
    public function edit($id)
    {
        try {
            // ดึงข้อมูลคำนำหน้าชื่อด้วย ID
            $prefix = Prefix::findOrFail($id);
            
            // บันทึก Log เพื่อการ Debug
            Log::info('Loading prefix edit form', [
                'id' => $id,
                'prefix_data' => $prefix->toArray()
            ]);
            
            // จัดการนับจำนวนพนักงานอย่างปลอดภัย
            $prefix->employees_count = 0;
            
            // ตรวจสอบว่ามีตาราง employees และมีคอลัมน์ prefix_id ก่อนทำ query
            if (Schema::hasTable('employees')) {
                if (Schema::hasColumn('employees', 'prefix_id')) {
                    $prefix->employees_count = \App\Models\Employee::where('prefix_id', $prefix->id)->count();
                } else {
                    Log::warning("Column 'prefix_id' not found in employees table");
                }
            } else {
                Log::warning("Table 'employees' not found in database");
            }
            
            return view('admin.prefixes.edit', compact('prefix'));
        } catch (\Exception $e) {
            Log::error('Error loading prefix for edit: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.prefixes.index')
                ->with('error', 'ไม่พบข้อมูลคำนำหน้าชื่อที่ต้องการแก้ไข: ' . $e->getMessage());
        }
    }

    /**
     * อัปเดตข้อมูลคำนำหน้าชื่อในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        try {
            // ค้นหาข้อมูลคำนำหน้าชื่อจาก ID
            $prefix = Prefix::findOrFail($id);
            
            // ตรวจสอบโครงสร้างตารางก่อนเพื่อปรับการ validate ให้เหมาะสม
            $hasPrefix_th = Schema::hasColumn('prefixes', 'prefix_th');
            
            if ($hasPrefix_th) {
                $validated = $request->validate([
                    'prefix_th' => 'required|string|max:50|unique:prefixes,prefix_th,'.$id,
                    'prefix_en' => 'nullable|string|max:50',
                    'description' => 'nullable|string|max:255',
                    'is_active' => 'nullable|boolean',
                ]);
                
                // เพิ่ม name เท่ากับ prefix_th สำหรับรองรับตารางเก่า
                $validated['name'] = $validated['prefix_th'];
            } else {
                $validated = $request->validate([
                    'name' => 'required|string|max:255|unique:prefixes,name,'.$id,
                    'description' => 'nullable|string|max:255',
                ]);
            }

            // กำหนดค่า is_active หากมีคอลัมน์นี้
            if (Schema::hasColumn('prefixes', 'is_active')) {
                $validated['is_active'] = $request->has('is_active') ? true : false;
            }

            DB::beginTransaction();
            
            // ใช้ update แทน forceFill เพื่อความชัดเจน
            $prefix->update($validated);
            
            DB::commit();
            
            Log::info('Prefix updated successfully', [
                'id' => $id, 
                'data' => $validated
            ]);
            
            return redirect()->route('admin.prefixes.index')
                ->with('success', 'แก้ไขคำนำหน้าชื่อสำเร็จ');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Prefix not found: ' . $e->getMessage());
            return redirect()->route('admin.prefixes.index')
                ->with('error', 'ไม่พบข้อมูลคำนำหน้าชื่อที่ต้องการแก้ไข');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating prefix: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลคำนำหน้าชื่อ
     */
    public function destroy($id)
    {
        try {
            $prefix = Prefix::findOrFail($id);
            
            // ตรวจสอบการใช้งานในตาราง employees อย่างปลอดภัย
            $employeeCount = 0;
            if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'prefix_id')) {
                $employeeCount = \App\Models\Employee::where('prefix_id', $prefix->id)->count();
            }
            
            if ($employeeCount > 0) {
                return back()->with('error', "ไม่สามารถลบได้: คำนำหน้าชื่อนี้กำลังถูกใช้งานโดยพนักงาน {$employeeCount} รายการ");
            }
            
            $prefix->delete();
            return redirect()->route('admin.prefixes.index')
                ->with('success', 'ลบคำนำหน้าชื่อสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error deleting prefix: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
