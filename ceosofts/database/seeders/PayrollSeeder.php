<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payroll;
use App\Models\Employee;

class PayrollSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            Payroll::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month_year'  => 'February-2025',
                ],
                [
                    'salary'                => $salary = rand(40000, 80000),
                    'allowance'             => $allowance = rand(0, 5000),
                    'bonus'                 => $bonus = rand(0, 5000),
                    'overtime'              => $overtime = rand(0, 3000),
                    'commission'            => $commission = rand(0, 4000),
                    'transport'             => $transport = rand(0, 1000),
                    'special_severance_pay' => 0,
                    'other_income'          => 0,
                    'total_income'          => $salary + $allowance + $bonus + $overtime + $commission + $transport,
                    'tax'                   => $tax = rand(0, 3000),
                    'social_fund'           => 750,
                    'provident_fund'        => 6000,
                    'telephone_bill'        => 0,
                    'house_rental'          => 0,
                    'no_pay_leave'          => 0,
                    'other_deductions'      => 0,
                    'total_deductions'      => $tax + 750 + 6000,
                    'net_income'            => ($salary + $allowance + $bonus + $overtime + $commission + $transport) - ($tax + 750 + 6000),
                    'ytd_income'            => 0,
                    'ytd_tax'               => 0,
                    'ytd_social_fund'       => 0,
                    'ytd_provident_fund'    => 0,
                    'remarks'               => 'Example payroll remark.'
                ]
            );
        }
    }
}
