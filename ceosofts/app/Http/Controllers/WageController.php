<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Wage;
use Carbon\Carbon;

class WageController extends Controller
{
    /**
     * แสดงหน้า 'สรุปค่าแรงพนักงาน'
     */
    public function index(Request $request)
    {
        // รับค่า month, year จาก query
        $month = $request->input('month', Carbon::now()->format('m')); // "03"
        $year  = $request->input('year',  Carbon::now()->format('Y')); // "2025"

        // สร้าง "YYYY-MM"
        $monthYear = $year . '-' . $month;

        // ดึง wages => keyBy employee_id
        $latestWages = Wage::where('month_year', $monthYear)
            ->orderBy('created_at', 'desc')
            ->get()
            ->keyBy('employee_id');

        // ดึง employees + attendances
        $employees = Employee::with(['attendances' => function ($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year);
        }])->get();

        // รวมข้อมูล
        $wageSummaries = $employees->map(function ($employee) use ($latestWages, $monthYear) {
            if (isset($latestWages[$employee->id])) {
                // ถ้าเจอใน wages -> ใช้เลย
                return $latestWages[$employee->id];
            }

            // ถ้าไม่เจอ -> คำนวณจาก attendance
            $dailyWage = $employee->salary / 30;
            $workDays  = $employee->attendances->count();
            $totalWage = $workDays * $dailyWage;

            $totalOT = $employee->attendances->sum('overtime_hours');
            $otRate  = ($dailyWage / 8) * 1.5;
            $otPay   = $totalOT * $otRate;

            $grandTotal = $totalWage + $otPay;

            // สร้าง object mock
            return (object) [
                'id'          => null,
                'employee_id' => $employee->id,
                'employee'    => $employee,
                'work_days'   => $workDays,
                'daily_wage'  => $dailyWage,
                'total_wage'  => $totalWage,
                'ot_hours'    => $totalOT,
                'ot_pay'      => $otPay,
                'grand_total' => $grandTotal,
                'status'      => 'pending',
                'month_year'  => $monthYear,
            ];
        });

        return view('wages.summary', [
            'wageSummaries' => $wageSummaries,
            'month' => $month,
            'year'  => $year,
        ]);
    }

    /**
     * บันทึกค่าแรงของเดือนนี้ (store monthly wages)
     */
    public function storeMonthlyWages(Request $request)
    {
        // รับ month, year จาก hidden input
        $month = $request->input('month', Carbon::now()->format('m'));
        $year  = $request->input('year',  Carbon::now()->format('Y'));
        $monthYear = $year . '-' . $month;

        // ดึง employees + attendance
        $employees = Employee::with(['attendances' => function ($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year);
        }])->get();

        foreach ($employees as $employee) {
            $dailyWage = $employee->salary / 30;
            $workDays  = $employee->attendances->count();
            $totalWage = $workDays * $dailyWage;

            $totalOT = $employee->attendances->sum('overtime_hours');
            $otRate  = ($dailyWage / 8) * 1.5;
            $otPay   = $totalOT * $otRate;

            $grandTotal = $totalWage + $otPay;

            // บันทึกลง wages
            Wage::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month_year'  => $monthYear,
                ],
                [
                    'work_days'   => $workDays,
                    'daily_wage'  => $dailyWage,
                    'total_wage'  => $totalWage,
                    'ot_hours'    => $totalOT,
                    'ot_pay'      => $otPay,
                    'grand_total' => $grandTotal,
                    'status'      => 'pending',
                ]
            );
        }

        return redirect()->back()->with('success', "ค่าแรงเดือน $monthYear ถูกบันทึกเรียบร้อยแล้ว!");
    }

    /**
     * สำหรับหน้า create payroll slip -> ดึงข้อมูลจาก wages
     */
    public function getWageData(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $month = $request->query('month');
        $year  = $request->query('year');

        $monthYear = $year . '-' . $month;  // "2025-03"

        $wage = Wage::where('employee_id', $employeeId)
            ->where('month_year', $monthYear)
            ->first();

        if ($wage) {
            // ตัวอย่าง: base_salary = daily_wage * 30
            return response()->json([
                'base_salary'                => $wage->daily_wage * 30,
                'accumulate_provident_fund'  => $wage->accumulate_provident_fund ?? 0,
                'accumulate_social_fund'     => $wage->accumulate_social_fund ?? 0,
                'commission'                 => $wage->commission ?? 0,
                'ot_hours'                   => $wage->ot_hours ?? 0,
            ]);
        } else {
            return response()->json([
                'base_salary'               => 0,
                'accumulate_provident_fund' => 0,
                'accumulate_social_fund'    => 0,
                'commission'                => 0,
                'ot_hours'                  => 0,
            ]);
        }
    }
}
