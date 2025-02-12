<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'first_name', 
        'last_name', 
        'email', 
        'national_id', 
        'driver_license',
        'date_of_birth', 
        'phone', 
        'address', 
        'emergency_contact_name',
        'emergency_contact_phone', 
        'spouse_name', 
        // 'children', 
        'tax_deductions',
        'department_id', 
        'position_id', 
        'salary', 
        'employment_status',
        'hire_date', 
        'resignation_date'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'resignation_date' => 'date',
        // 'children' => 'array', // ✅ ให้ Laravel จัดการ JSON เป็น array อัตโนมัติ
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            // ✅ ตรวจสอบเฉพาะพนักงานที่มี employee_code เท่านั้น
            $latestEmployee = Employee::whereNotNull('employee_code')->latest('id')->first();
            $nextNumber = $latestEmployee ? intval(substr($latestEmployee->employee_code, 3)) + 1 : 1;
            $employee->employee_code = 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * ✅ คำนวณอายุจาก date_of_birth
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    /**
     * ✅ Mutator: ตั้งค่าลูก (Children) ให้เป็น JSON ก่อนบันทึก
     */
    // public function setChildrenAttribute($value)
    // {
    //     $this->attributes['children'] = is_array($value) ? json_encode($value) : $value;
    // }

    /**
     * ✅ Accessor: ดึงค่าลูก (Children) เป็น Array
     */
    // public function getChildrenAttribute($value)
    // {
    //     return json_decode($value, true) ?? [];
    // }
}