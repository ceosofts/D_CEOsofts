<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Wage;
use Carbon\Carbon;

class WageController extends Controller
{

public function index(Request $request)
{
    $month = $request->input('month', Carbon::now()->format('m'));
    $year = $request->input('year', Carbon::now()->format('Y'));

    // ✅ ดึงพนักงานทั้งหมด และเข้าถึง `attendances`
    $employees = Employee::with(['attendances' => function ($query) use ($month, $year) {
        $query->whereMonth('date', $month)->whereYear('date', $year);
    }])->get();

    // ✅ ดึงค่าแรงล่าสุดจาก `wages`
    $latestWages = Wage::whereMonth('month_year', $month)
        ->whereYear('month_year', $year)
        ->orderBy('created_at', 'desc') // ดึงข้อมูลล่าสุด
        ->get()
        ->keyBy('employee_id'); // จับคู่ตาม employee_id

    // ✅ รวมข้อมูลที่ดึงมา ถ้ามีค่าแรงแล้วให้ใช้ ถ้ายังไม่มีให้คำนวณใหม่
    $wageSummaries = $employees->map(function ($employee) use ($latestWages) {
        if (isset($latestWages[$employee->id])) {
            return $latestWages[$employee->id]; // ใช้ข้อมูลที่เคยบันทึกแล้ว
        }

        // คำนวณจาก attendance ถ้ายังไม่มีใน wages
        $dailyWage = $employee->salary / 30;
        $workDays = $employee->attendances->count();
        $totalWage = $workDays * $dailyWage;
        $totalOT = $employee->attendances->sum('overtime_hours');
        $otRate = ($dailyWage / 8) * 1.5;
        $otPay = $totalOT * $otRate;
        $grandTotal = $totalWage + $otPay;

        return (object) [
            'employee' => $employee,
            'work_days' => $workDays,
            'daily_wage' => $dailyWage,
            'total_wage' => $totalWage,
            'ot_hours' => $totalOT,
            'ot_pay' => $otPay,
            'grand_total' => $grandTotal,
            'status' => 'pending',
        ];
    });

    
    return view('wages.summary', compact('wageSummaries', 'month', 'year'));
}


public function storeMonthlyWages()
{
    $month = Carbon::now()->format('Y-m'); // ใช้ YYYY-MM
    $employees = Employee::with(['attendances' => function ($query) {
        $query->whereMonth('date', Carbon::now()->month);
    }])->get();

    foreach ($employees as $employee) {
        $dailyWage = $employee->salary / 30;
        $workDays = $employee->attendances->count();
        $totalWage = $workDays * $dailyWage;
        $totalOT = $employee->attendances->sum('overtime_hours');
        $otRate = ($dailyWage / 8) * 1.5;
        $otPay = $totalOT * $otRate;
        $grandTotal = $totalWage + $otPay;

        // ✅ แทนที่ `create()` ด้วย `updateOrCreate()`
        Wage::updateOrCreate(
            ['employee_id' => $employee->id, 'month_year' => $month], // คีย์หลัก
            [
                'work_days' => $workDays ?? 0,
                'daily_wage' => $dailyWage ?? 0,
                'total_wage' => $totalWage ?? 0,
                'ot_hours' => $totalOT ?? 0,
                'ot_pay' => $otPay ?? 0,
                'grand_total' => $grandTotal ?? 0,
                'status' => 'pending'
            ]
        );
    }

    return redirect()->back()->with('success', 'ค่าแรงเดือนนี้ถูกบันทึกเรียบร้อยแล้ว!');
}


}
