<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    /**
     * แสดงรายการแผนกทั้งหมด
     */
    public function index()
    {
        // ใช้ get() แทน paginate() เพื่อดึงข้อมูลทั้งหมด
        $departments = Department::get();
        return \view('admin.departments.index', compact('departments'));
    }

    /**
     * แสดงฟอร์มสร้างแผนกใหม่
     */
    public function create()
    {
        return \view('admin.departments.create');
    }

    /**
     * บันทึกข้อมูลแผนกใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:departments|max:255',
        ]);

        try {
            $department = new Department();
            $department->fill($validated);
            $department->save();

            return \redirect()->route('admin.departments.index')
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            Log::error('Error storing department: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลแผนก
     */
    public function edit(Department $department)
    {
        // เพิ่มการดึงข้อมูลพนักงานในแผนก
        $departmentUsers = \App\Models\User::where('department_id', $department->id)->get();
        $departmentUserCount = $departmentUsers->count();
        
        return view('admin.departments.edit', compact('department', 'departmentUsers', 'departmentUserCount'));
    }

    /**
     * อัปเดตข้อมูลแผนก
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|unique:departments,name,' . $department->id . '|max:255',
        ]);

        try {
            // ใช้ forceFill เพื่อบังคับอัปเดตข้อมูล
            $department->forceFill($validated);
            $department->updated_at = \now();
            $department->save();

            return \redirect()->route('admin.departments.index')
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating department: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลแผนก
     */
    public function destroy(Department $department)
    {
        try {
            $department->delete();
            return \redirect()->route('admin.departments.index')
                ->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting department: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
