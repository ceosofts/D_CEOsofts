<?php

namespace App\Http\Controllers;  // ✅ เพิ่มบรรทัดนี้ถ้ายังไม่มี

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;
use App\Http\Controllers\Controller; // ✅ เพิ่มบรรทัดนี้

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees', 
            'national_id' => 'nullable|string|size:13|unique:employees',
            'driver_license' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'spouse_name' => 'nullable|string|max:255',
            'children' => 'nullable|string',
            'tax_deductions' => 'nullable|numeric|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'employment_status' => 'nullable|in:active,resigned,terminated,on_leave',
            'hire_date' => 'nullable|date',
            'resignation_date' => 'nullable|date|after:hire_date',
        ]);

        // ✅ ปรับ children ให้เป็น JSON
        $validatedData['children'] = $request->children ? json_encode(explode(',', $request->children)) : json_encode([]);

        Employee::create($validatedData);

        return redirect()->route('employees.index')->with('success', 'พนักงานถูกเพิ่มเรียบร้อยแล้ว');
    }

    public function update(Request $request, Employee $employee)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'children' => 'nullable|string',
        ]);

        $validatedData['children'] = $request->children ? json_encode(explode(',', $request->children)) : json_encode([]);

        $employee->update($validatedData);

        return redirect()->route('employees.index')->with('success', 'พนักงานถูกอัปเดตเรียบร้อยแล้ว');
    }
}
