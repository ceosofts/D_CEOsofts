<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Wage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WageController extends Controller
{
    /**
     * แสดงหน้า 'สรุปค่าแรงพนักงาน'
     */
    public function index(Request $request)
    {
        // 1) รับค่าค้นหาจาก input
        $search = $request->input('search', '');

        // 2) รับค่า month, year จาก query หรือใช้ค่าปัจจุบันเป็น default
        $month = $request->input('month', Carbon::now()->format('m'));
        $year  = $request->input('year',  Carbon::now()->format('Y'));

        // สร้าง "YYYY-MM"
        $monthYear = $year . '-' . $month;

        try {
            // ดึงข้อมูล wages ที่มี month_year ตรงกับ $monthYear
            // แล้ว keyBy('employee_id') เพื่อเรียกใช้ง่าย
            $latestWages = Wage::where('month_year', $monthYear)
                ->orderBy('created_at', 'desc')
                ->get()
                ->keyBy('employee_id');

            // ดึงพนักงานทั้งหมด
            // หากต้องการ filter ด้วย $search สามารถเพิ่ม where() หรือ filter() ได้
            // ตัวอย่าง filter ชื่อพนักงาน หรือ code
            $employeesQuery = Employee::with(['attendances' => function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            }]);

            if (!empty($search)) {
                $employeesQuery->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('employee_code', 'LIKE', "%{$search}%");
                });
            }

            $employees = $employeesQuery->get();

            // รวมข้อมูล wageSummaries
            $wageSummaries = $employees->map(function ($employee) use ($latestWages, $monthYear) {
                // ถ้าใน wages มี record -> ใช้ record นั้น
                if (isset($latestWages[$employee->id])) {
                    // ดึง record จาก DB ที่มีอยู่
                    return $latestWages[$employee->id];
                }

                // ถ้าไม่มี -> คำนวณจาก attendance
                $dailyWage = $employee->salary / 30;
                $workDays  = $employee->attendances->count();
                $totalWage = $workDays * $dailyWage;

                $totalOT = $employee->attendances->sum('overtime_hours');
                $otRate  = ($dailyWage / 8) * 1.5;
                $otPay   = $totalOT * $otRate;

                $grandTotal = $totalWage + $otPay;

                // mock object (ยังไม่บันทึกใน DB)
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
                'month'         => $month,
                'year'          => $year,
                'search'        => $search,  // ส่งตัวแปร search ไปที่ Blade
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching wage summaries: ' . $e->getMessage(), [
                'month' => $month,
                'year'  => $year
            ]);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการดึงข้อมูลค่าแรง: ' . $e->getMessage()]);
        }
    }

    /**
     * บันทึกค่าแรงของเดือนนี้ (store monthly wages)
     */
    public function storeMonthlyWages(Request $request)
    {
        // รับ month, year จาก form หรือใช้ปัจจุบัน
        $month = $request->input('month', Carbon::now()->format('m'));
        $year  = $request->input('year',  Carbon::now()->format('Y'));
        $monthYear = $year . '-' . $month;

        try {
            DB::transaction(function () use ($month, $year, $monthYear) {
                // ดึง employee + attendance
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

                    // บันทึกลง wages (updateOrCreate)
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
            });

            return redirect()->back()
                ->with('success', "ค่าแรงเดือน {$monthYear} ถูกบันทึกเรียบร้อยแล้ว!");
        } catch (\Exception $e) {
            Log::error('Error storing monthly wages: ' . $e->getMessage(), $request->all());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกค่าแรง: ' . $e->getMessage()]);
        }
    }

    /**
     * สำหรับหน้า create payroll slip -> ดึงข้อมูลจาก wages (AJAX)
     */
    public function getWageData(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $month = $request->query('month');
        $year  = $request->query('year');

        $monthYear = $year . '-' . $month; // เช่น "2025-03"

        try {
            $wage = Wage::where('employee_id', $employeeId)
                ->where('month_year', $monthYear)
                ->first();

            if ($wage) {
                // ตัวอย่าง base_salary = daily_wage * 30
                return response()->json([
                    'base_salary'               => $wage->daily_wage * 30,
                    'accumulate_provident_fund' => $wage->accumulate_provident_fund ?? 0,
                    'accumulate_social_fund'    => $wage->accumulate_social_fund ?? 0,
                    'commission'                => $wage->commission ?? 0,
                    'ot_hours'                  => $wage->ot_hours ?? 0,
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
        } catch (\Exception $e) {
            Log::error('Error fetching wage data (AJAX): ' . $e->getMessage(), $request->all());
            return response()->json([
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูลค่าแรง: ' . $e->getMessage()
            ], 500);
        }
    }
}
