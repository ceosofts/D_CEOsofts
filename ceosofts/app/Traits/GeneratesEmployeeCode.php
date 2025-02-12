<?php

namespace App\Traits;

use App\Models\Employee;

trait GeneratesEmployeeCode
{
    /**
     * กำหนด employee_code อัตโนมัติเมื่อสร้างพนักงานใหม่
     */
    protected static function bootGeneratesEmployeeCode()
    {
        static::creating(function ($employee) {
            $employee->employee_code = self::generateEmployeeCode();
        });
    }

    /**
     * สร้าง employee_code ใหม่โดยอ้างอิงจากรหัสล่าสุด
     *
     * @return string
     */
    protected static function generateEmployeeCode()
    {
        $latestEmployee = Employee::whereNotNull('employee_code')->latest('id')->first();
        $nextNumber = $latestEmployee ? intval(substr($latestEmployee->employee_code, 3)) + 1 : 1;
        return 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
