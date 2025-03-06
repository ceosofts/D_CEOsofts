<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon; // ใช้ Carbon สำหรับจัดการเวลา

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'work_hours',
        'work_hours_completed',
        'overtime_hours',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            if (!empty($attendance->check_in) && !empty($attendance->check_out)) {
                $checkInTime  = Carbon::parse($attendance->check_in);
                $checkOutTime = Carbon::parse($attendance->check_out);

                // คำนวณชั่วโมงการทำงาน
                $workHours = $checkInTime->diffInMinutes($checkOutTime) / 60;

                // ป้องกันค่าติดลบ พร้อมปัดเป็นทศนิยม 2 ตำแหน่ง
                $attendance->work_hours = max(round($workHours, 2), 0);

                // ทำงานครบ 8 ชั่วโมงหรือไม่
                $attendance->work_hours_completed = $attendance->work_hours >= 8;

                // ชั่วโมงที่เกิน 8 ถือเป็น OT
                $attendance->overtime_hours = max($attendance->work_hours - 8, 0);
            }
        });
    }

    /**
     * Relationship: Attendance belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
