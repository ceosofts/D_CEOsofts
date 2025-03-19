<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง payrolls
        if (!Schema::hasTable('payrolls')) {
            $this->command->error("Table 'payrolls' does not exist, skipping seeder.");
            return;
        }

        // ตรวจสอบว่ามีตาราง employees หรือไม่
        if (!Schema::hasTable('employees')) {
            $this->command->error("Table 'employees' does not exist, skipping seeder.");
            return;
        }

        // ตรวจสอบว่ามีข้อมูลใน employees หรือไม่
        $employeeCount = DB::table('employees')->count();
        if ($employeeCount == 0) {
            $this->command->error("No employees found, please seed employees first.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็นในตาราง payrolls
            if (Schema::hasTable('payrolls')) {
                $columns = Schema::getColumnListing('payrolls');
                $requiredColumns = ['employee_id', 'period_month', 'period_year', 'basic_salary', 'net_salary'];
                $missingColumns = array_diff($requiredColumns, $columns);
                
                if (!empty($missingColumns)) {
                    $this->command->error("Missing columns in payrolls table: " . implode(', ', $missingColumns));
                    return;
                }
            }

            // สร้างข้อมูลเงินเดือนตัวอย่างสำหรับพนักงานคนแรก
            $employee = DB::table('employees')->first();
            if (!$employee) {
                $this->command->error("No employee found.");
                return;
            }

            // เดือนปัจจุบันและเดือนก่อนหน้า
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $previousMonth = Carbon::now()->subMonth()->month;
            $previousMonthYear = Carbon::now()->subMonth()->year;

            // ข้อมูลเงินเดือนตัวอย่าง
            $payrollsData = [
                [
                    'employee_id' => $employee->id,
                    'period_month' => $previousMonth,
                    'period_year' => $previousMonthYear,
                    'basic_salary' => 30000,
                    'overtime_pay' => 1500,
                    'bonus' => 0,
                    'allowance' => 1000,
                    'tax_deduction' => 1800,
                    'social_security' => 750,
                    'other_deduction' => 0,
                    'net_salary' => 29950,
                    'payment_status' => 'paid',
                    'payment_date' => Carbon::create($previousMonthYear, $previousMonth, 28)->format('Y-m-d'),
                    'remarks' => 'เงินเดือนเดือน ' . Carbon::create()->month($previousMonth)->locale('th')->monthName,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'employee_id' => $employee->id,
                    'period_month' => $currentMonth,
                    'period_year' => $currentYear,
                    'basic_salary' => 30000,
                    'overtime_pay' => 2000,
                    'bonus' => 5000,
                    'allowance' => 1000,
                    'tax_deduction' => 2000,
                    'social_security' => 750,
                    'other_deduction' => 500,
                    'net_salary' => 34750,
                    'payment_status' => 'pending',
                    'payment_date' => null,
                    'remarks' => 'เงินเดือนเดือน ' . Carbon::create()->month($currentMonth)->locale('th')->monthName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];

            // เพิ่มข้อมูล และตรวจสอบก่อนเพิ่มเพื่อป้องกันการซ้ำ
            $count = 0;
            foreach ($payrollsData as $payroll) {
                $exists = DB::table('payrolls')
                    ->where('employee_id', $payroll['employee_id'])
                    ->where('period_month', $payroll['period_month'])
                    ->where('period_year', $payroll['period_year'])
                    ->exists();
                    
                if (!$exists) {
                    DB::table('payrolls')->insert($payroll);
                    $count++;
                }
            }

            $this->command->info("Successfully seeded {$count} payrolls");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
