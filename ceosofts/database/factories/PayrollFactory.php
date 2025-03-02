<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payroll;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition()
    {
        return [
            'employee_id' => 1, // ค่อย override ทีหลัง
            'month_year' => 'February-2025',
            'salary' => $this->faker->randomFloat(2, 30000, 70000),
            'allowance' => $this->faker->randomFloat(2, 0, 2000),
            'bonus' => $this->faker->randomFloat(2, 0, 5000),
            'overtime' => $this->faker->randomFloat(2, 0, 3000),
            'commission' => $this->faker->randomFloat(2, 0, 4000),
            'transport' => $this->faker->randomFloat(2, 0, 1000),
            'special_severance_pay' => 0,
            'other_income' => 0,
            'total_income' => 0, // ไว้คำนวณหรือปรับทีหลัง
            'tax' => $this->faker->randomFloat(2, 500, 3000),
            'social_fund' => 750,
            'provident_fund' => 6000,
            'telephone_bill' => 0,
            'house_rental' => 0,
            'no_pay_leave' => 0,
            'other_deductions' => 0,
            'total_deductions' => 0, // ไว้คำนวณหรือปรับทีหลัง
            'net_income' => 0,
            'ytd_income' => 0,
            'ytd_tax' => 0,
            'ytd_social_fund' => 0,
            'ytd_provident_fund' => 0,
            'remarks' => $this->faker->sentence,
        ];
    }
}
