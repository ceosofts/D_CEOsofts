<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'work_shift_id', 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class);
    }
}
