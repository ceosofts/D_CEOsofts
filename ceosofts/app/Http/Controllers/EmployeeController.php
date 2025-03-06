<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * แสดงรายการพนักงานทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $employees = Employee::with(['department', 'position'])->paginate(10);
        return \view('employees.index', compact('employees'));
    }

    /**
     * แสดงฟอร์มสร้างพนักงานใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return \view('employees.create', [
            'departments'   => Department::all(),
            'positions'     => Position::all(),
            'employee_code' => Employee::generateEmployeeCode(),
            'editMode'      => false,
            'employee'      => null,
        ]);
    }

    /**
     * บันทึกพนักงานใหม่
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EmployeeRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        // กำหนดรหัสพนักงาน หากไม่ได้ส่งมาจากฟอร์ม
        $validatedData['employee_code'] = $request->employee_code ?? Employee::generateEmployeeCode();
        // กำหนดสถานะเริ่มต้นเป็น active หากไม่ได้ระบุ
        $validatedData['employment_status'] = $validatedData['employment_status'] ?? 'active';

        Employee::create($validatedData);

        return \redirect()->route('employees.index')
            ->with('success', 'พนักงานถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มแก้ไขพนักงาน
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\View\View
     */
    public function edit(Employee $employee): View
    {
        return \view('employees.edit', [
            'employee'    => $employee,
            'departments' => Department::all(),
            'positions'   => Position::all(),
            'editMode'    => true,
        ]);
    }

    /**
     * อัปเดตข้อมูลพนักงาน
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return \redirect()->route('employees.index')
            ->with('success', 'พนักงานถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * ลบพนักงาน
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();
        return \redirect()->route('employees.index')
            ->with('success', 'พนักงานถูกลบเรียบร้อยแล้ว');
    }
}
