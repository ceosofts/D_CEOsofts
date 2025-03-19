<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class UnitController extends Controller
{
    /**
     * Display a listing of the units.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
            DB::connection()->getPdo();
            
            $query = Unit::query();
            
            // การค้นหา
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    // ตรวจสอบคอลัมน์ที่มีในตาราง
                    if (Schema::hasColumn('units', 'unit_code')) {
                        $q->where('unit_code', 'like', "%{$searchTerm}%");
                    } else if (Schema::hasColumn('units', 'unit_name_th')) {
                        $q->where('unit_name_th', 'like', "%{$searchTerm}%");
                    }
                    
                    if (Schema::hasColumn('units', 'unit_name')) {
                        $q->orWhere('unit_name', 'like', "%{$searchTerm}%");
                    } else if (Schema::hasColumn('units', 'unit_name_en')) {
                        $q->orWhere('unit_name_en', 'like', "%{$searchTerm}%");
                    }
                    
                    if (Schema::hasColumn('units', 'name')) {
                        $q->orWhere('name', 'like', "%{$searchTerm}%");
                    }
                });
            }
            
            // เรียงลำดับตาม ID เสมอ
            $query->orderBy('id', 'asc');
            
            // การแบ่งหน้า - ใช้ paginate แทน get เพื่อประสิทธิภาพ
            $units = $query->paginate(15);
            
            return view('admin.units.index', compact('units'));
            
        } catch (QueryException $e) {
            // กรณีเกิดปัญหากับ query
            Log::error('Database query error in UnitController@index: ' . $e->getMessage());
            return view('admin.units.index', [
                'units' => collect([]),
                'error' => 'เกิดข้อผิดพลาดในการเรียกข้อมูล: ' . $e->getMessage()
            ]);
        } catch (\PDOException $e) {
            // กรณีเชื่อมต่อ Database ไม่ได้
            Log::error('Database connection error in UnitController@index: ' . $e->getMessage());
            return view('admin.units.index', [
                'units' => collect([]),
                'error' => 'ไม่สามารถเชื่อมต่อกับฐานข้อมูล: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            // กรณีเกิด Error อื่นๆ
            Log::error('General error in UnitController@index: ' . $e->getMessage());
            return view('admin.units.index', [
                'units' => collect([]),
                'error' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * แสดงฟอร์มสร้างหน่วยวัดใหม่
     */
    public function create()
    {
        return view('admin.units.create');
    }

    /**
     * บันทึกข้อมูลหน่วยวัดใหม่
     */
    public function store(Request $request)
    {
        // ปรับ validation ตามคอลัมน์ที่มี
        $rules = [];
        
        if (Schema::hasColumn('units', 'unit_name_th')) {
            $rules['unit_name_th'] = 'required|string|max:50|unique:units,unit_name_th';
            $rules['unit_name_en'] = 'nullable|string|max:50';
        } elseif (Schema::hasColumn('units', 'name')) {
            $rules['name'] = 'required|string|max:50|unique:units,name';
        }
        
        $rules['description'] = 'nullable|string|max:255';
        $rules['is_active'] = 'nullable|boolean';
        
        $validatedData = $request->validate($rules);
        
        // กำหนดค่า default สำหรับ is_active
        if (Schema::hasColumn('units', 'is_active')) {
            $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;
        }
        
        try {
            // บันทึกข้อมูล
            Unit::create($validatedData);
            
            return redirect()->route('admin.units.index')
                ->with('success', 'เพิ่มหน่วยวัดสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error creating unit: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * แสดงข้อมูลหน่วยวัด
     */
    public function show(Unit $unit)
    {
        return view('admin.units.show', compact('unit'));
    }

    /**
     * แสดงฟอร์มแก้ไขหน่วยวัด
     */
    public function edit(Unit $unit)
    {
        try {
            // นับจำนวนสินค้าที่ใช้หน่วยนี้
            $unit->products_count = Product::where('unit_id', $unit->id)->count();
            
            return view('admin.units.edit', compact('unit'));
        } catch (\Exception $e) {
            Log::error('Error loading unit for edit: ' . $e->getMessage());
            return redirect()->route('admin.units.index')
                ->with('error', 'ไม่พบข้อมูลหน่วยวัดที่ต้องการแก้ไข');
        }
    }

    /**
     * อัปเดตข้อมูลหน่วยวัด
     */
    public function update(Request $request, Unit $unit)
    {
        // Validate ข้อมูล
        $validatedData = $request->validate([
            'unit_name_th' => 'required|string|max:50|unique:units,unit_name_th,'.$unit->id,
            'unit_name_en' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ]);
        
        // กำหนดค่า default สำหรับ is_active
        $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;
        
        try {
            $unit->update($validatedData);
            
            return redirect()->route('admin.units.index')
                ->with('success', 'อัปเดตหน่วยวัดสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error updating unit: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * ลบหน่วยวัด
     */
    public function destroy(Unit $unit)
    {
        try {
            // ตรวจสอบว่ามีการใช้งานในสินค้าหรือไม่
            $productCount = Product::where('unit_id', $unit->id)->count();
            
            if ($productCount > 0) {
                return back()->with('error', "ไม่สามารถลบได้: หน่วยวัดนี้กำลังถูกใช้งานโดยสินค้า {$productCount} รายการ");
            }
            
            $unit->delete();
            return redirect()->route('admin.units.index')
                ->with('success', 'ลบหน่วยวัดสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error deleting unit: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
