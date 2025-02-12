<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_days',
        'daily_wage',
        'total_wage',
        'ot_hours',
        'ot_pay',
        'grand_total',
        'month_year',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
        
    }
}
