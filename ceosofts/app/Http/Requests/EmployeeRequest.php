<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? null;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => "nullable|email|unique:employees,email,{$employeeId}",
            'national_id' => "nullable|string|size:13|unique:employees,national_id,{$employeeId}",
            'driver_license' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'spouse_name' => 'nullable|string|max:255',
            'tax_deductions' => 'nullable|numeric|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'salary' => 'nullable|numeric|min:0',
            'employment_status' => 'nullable|in:active,resigned,terminated,on_leave',
            'hire_date' => 'nullable|date',
            'resignation_date' => 'nullable|date|after:hire_date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
