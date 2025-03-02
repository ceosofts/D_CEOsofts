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

        // ถ้าคุณมีฟิลด์อื่น (accumulate_provident_fund, commission, etc.) ใส่ที่นี่ด้วย
        // 'accumulate_provident_fund',
        // 'accumulate_social_fund',
        // 'commission',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
