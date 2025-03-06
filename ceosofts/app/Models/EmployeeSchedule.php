<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\WorkShift;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_shift_id',
        'date'
    ];

    // Cast ฟิลด์ date เป็นประเภท date
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relationship: Schedule belongs to an Employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relationship: Schedule belongs to a WorkShift.
     */
    public function workShift()
    {
        return $this->belongsTo(WorkShift::class);
    }
}
