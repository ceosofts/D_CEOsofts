<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    /**
     * ฟิลด์ที่อนุญาตให้ทำ Mass Assignment
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'month_year',
        'salary',
        'overtime',
        'bonus',
        'commission',
        'transport',
        'special_severance_pay',
        'other_income',
        'tax',
        'social_fund',
        'provident_fund',
        'telephone_bill',
        'house_rental',
        'no_pay_leave',
        'other_deductions',
        'total_income',
        'total_deductions',
        'net_income',
        'ytd_income',
        'ytd_tax',
        'ytd_social_fund',
        'ytd_provident_fund',
        'accumulate_provident_fund',
        'accumulate_social_fund',
        'remarks',
        'prepared_by',
    ];

    /**
     * Relationship: แสดงความสัมพันธ์ระหว่าง Payroll กับ Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
        // ในกรณีที่ชื่อ foreign key เป็น employee_id และ primary key เป็น id สามารถเรียกใช้งานแบบนี้ได้เลย
    }
}
