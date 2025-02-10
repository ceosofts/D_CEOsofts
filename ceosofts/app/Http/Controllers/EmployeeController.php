<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * ✅ แสดงรายการพนักงานทั้งหมด
     */
    public function index()
    {
        $employees = Employee::with(['department', 'position'])->paginate(10);
        return view('employees.index', compact('employees'));
    }

    /**
     * ✅ แสดงฟอร์มสร้างพนักงานใหม่
     */
    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        $employee = null; // ✅ ป้องกันข้อผิดพลาดของตัวแปรที่เป็น null
        $editMode = false;
        return view('employees.create', compact('departments', 'positions', 'employee', 'editMode'));
    }

    /**
     * ✅ บันทึกพนักงานใหม่ (เฉพาะ First Name & Last Name เป็นบังคับ)
     */

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'first_name' => 'required|string|max:255',  // ✅ บังคับ
        'last_name' => 'required|string|max:255',   // ✅ บังคับ
        'email' => 'nullable|email|unique:employees', 
        'national_id' => 'nullable|string|size:13|unique:employees',
        'driver_license' => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'spouse_name' => 'nullable|string|max:255',
        // 'children' => 'nullable|string',
        'tax_deductions' => 'nullable|numeric|min:0',
        'department_id' => 'nullable|exists:departments,id',
        'position_id' => 'nullable|exists:positions,id',
        'salary' => 'nullable|numeric|min:0',
        'employment_status' => 'nullable|in:active,resigned,terminated,on_leave',
        'hire_date' => 'nullable|date',
        'resignation_date' => 'nullable|date|after:hire_date',
    ]);

    // ✅ แปลงค่า NULL เป็นค่าเริ่มต้น
    // $validatedData['children'] = $request->has('children') ? json_encode($request->children) : json_encode([]);
    // $validatedData['children'] = $request->children ? json_encode(explode(',', $request->children)) : json_encode([]);
    
    
    $validatedData['national_id'] = $request->national_id ?? null;
    $validatedData['email'] = $request->email ?? null;
    $validatedData['department_id'] = $request->department_id ?? null;
    $validatedData['employment_status'] = $request->employment_status ?? 'active'; // ✅ ค่าปริยายเป็น 'active'

    // ✅ บันทึกข้อมูล
    Employee::create($validatedData);

    return redirect()->route('employees.index')->with('success', 'พนักงานถูกเพิ่มเรียบร้อยแล้ว');
}


    /**
     * ✅ แสดงฟอร์มแก้ไขพนักงาน
     */
    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = Position::all();
        $editMode = true;
        return view('employees.edit', compact('employee', 'departments', 'positions', 'editMode'));
    }

    /**
     * ✅ อัปเดตข้อมูลพนักงาน (เฉพาะ First Name & Last Name เป็นบังคับ)
     */
    public function update(Request $request, Employee $employee)
    {
        // $request->validate([
            $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',  // ✅ บังคับ
            'last_name' => 'required|string|max:255',   // ✅ บังคับ
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'national_id' => 'nullable|string|size:13|unique:employees,national_id,' . $employee->id,
            'driver_license' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'spouse_name' => 'nullable|string|max:255',
            // 'children' => 'nullable|string',
            'tax_deductions' => 'nullable|numeric|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'salary' => 'nullable|numeric|min:0',
            'employment_status' => 'nullable|in:active,resigned,terminated,on_leave',
            'hire_date' => 'nullable|date',
            'resignation_date' => 'nullable|date|after:hire_date',
        ]);

        // 🔹 แปลง children เป็น JSON
        // $validatedData = $request->all();
        // $validatedData['children'] = $request->has('children') ? json_encode($request->children) : json_encode([]);

        // 🔹 อัปเดตข้อมูลพนักงาน
        $employee->update($validatedData);

        return redirect()->route('employees.index')->with('success', 'พนักงานถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * ✅ ลบพนักงาน
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'พนักงานถูกลบเรียบร้อยแล้ว');
    }
}
