<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Traits\GeneratesEmployeeCode;

class Employee extends Model
{
    use HasFactory, GeneratesEmployeeCode;

    /**
     * กำหนดค่า fillable สำหรับ Mass Assignment
     */
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
        'tax_deductions',
        'department_id',
        'position_id',
        'salary',
        'employment_status',
        'hire_date',
        'resignation_date'
    ];

    /**
     * ✅ Relationship: เชื่อมกับตาราง attendances (การเข้างาน)
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    
    /**
     * แปลงค่าฟิลด์วันที่เป็น Carbon instance
     */
    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
        'hire_date' => 'datetime:Y-m-d',
        'resignation_date' => 'datetime:Y-m-d',
    ];

    /**
     * ความสัมพันธ์กับ Department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * ความสัมพันธ์กับ Position
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Accessor: คำนวณอายุจาก date_of_birth
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    public static function generateEmployeeCode()
    {
        $latestEmployee = self::whereNotNull('employee_code')->latest('id')->first();
        $nextNumber = $latestEmployee ? intval(substr($latestEmployee->employee_code, 3)) + 1 : 1;
        return 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

}
