<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wage
 *
 * This model represents a wage record for an employee for a given month.
 *
 * @property int $employee_id
 * @property int $work_days
 * @property float $daily_wage
 * @property float $total_wage
 * @property float $ot_hours
 * @property float $ot_pay
 * @property float $grand_total
 * @property string $month_year  Format: "YYYY-MM"
 * @property string $status
 * 
 * // Additional optional properties (if used):
 * // @property float|null $accumulate_provident_fund
 * // @property float|null $accumulate_social_fund
 * // @property float|null $commission
 */
class Wage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        // Uncomment and add these if you wish to store additional fields:
        // 'accumulate_provident_fund',
        // 'accumulate_social_fund',
        // 'commission',
    ];

    /**
     * Get the employee that owns this wage record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
