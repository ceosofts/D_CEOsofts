<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Wage; // ถ้าคุณมี Model Wage
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    /**
     * แสดงรายการสลิปเงินเดือน
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $month  = $request->input('month', '');
        $year   = $request->input('year', '');

        $query = Payroll::with('employee');

        // ถ้ามีข้อความค้นหา
        if (!empty($search)) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_code', 'LIKE', "%$search%")
                    ->orWhere('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%");
            });
        }

        // ถ้าเลือก month/year
        if (!empty($month) && !empty($year)) {
            $monthYear = $year . '-' . $month; // "2025-02"
            $query->where('month_year', $monthYear);
        }

        $payrolls = $query->paginate(10);

        return view('payrolls.payroll-index', [
            'payrolls' => $payrolls,
            'search'   => $search,
            'month'    => $month,
            'year'     => $year,
        ]);
    }

    /**
     * แสดงฟอร์มสร้างสลิปเงินเดือนใหม่
     */
    public function create(Request $request)
    {
        $employees = \App\Models\Employee::all();

        $empId = $request->query('employee_id');
        $month = $request->query('month');
        $year  = $request->query('year');

        $autoFill = [
            'salary'                => 0,
            'overtime'              => 0,
            'bonus'                 => 0,
            'commission'            => 0,
            'transport'             => 0,
            'special_severance_pay' => 0,
            'other_income'          => 0,

            'tax'                   => 0,
            'social_fund'          => 0,
            'provident_fund'       => 0,
            'telephone_bill'        => 0,
            'house_rental'          => 0,
            'no_pay_leave'          => 0,
            'other_deductions'      => 0,

            'total_income'          => 0,
            'total_deductions'      => 0,
            'net_income'            => 0,

            'accumulate_provident_fund' => 0,
            'accumulate_social_fund'    => 0,
            'remarks'               => '',
        ];

        if ($empId && $month && $year) {
            $monthYear = $year . '-' . $month;
            // สมมติจะดึงจากตาราง wages หรือที่อื่น
            $wage = \App\Models\Wage::where('employee_id', $empId)
                ->where('month_year', $monthYear)
                ->first();
            if ($wage) {
                // ตัวอย่าง mapping
                $autoFill['salary']    = $wage->total_wage;
                $autoFill['overtime']  = $wage->ot_pay;
                $autoFill['total_income'] = $wage->grand_total;

                // สมมติ provident_fund เก็บเป็น accumulate_provident_fund
                $autoFill['accumulate_provident_fund'] = $wage->provident_fund ?? 0;
                // เป็นต้น
            }
        }

        return view('payrolls.payroll-create', [
            'employees'          => $employees,
            'selectedEmployeeId' => $empId,
            'selectedMonth'      => $month,
            'selectedYear'       => $year,
            'autoFill'           => $autoFill,
        ]);
    }

    /**
     * บันทึกสลิปเงินเดือนใหม่
     */
    public function store(Request $request)
    {
        // Validate input จากฟอร์ม
        $validated = $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'month'            => 'required|string',
            'year'             => 'required|digits:4',
            'salary'           => 'nullable|numeric',
            'overtime'         => 'nullable|numeric',
            'bonus'            => 'nullable|numeric',
            'commission'       => 'nullable|numeric',
            'transport'        => 'nullable|numeric',
            'special_severance_pay' => 'nullable|numeric',
            'other_income'     => 'nullable|numeric',
            'total_income'     => 'required|numeric',
            'tax'              => 'nullable|numeric',
            'social_fund'      => 'nullable|numeric',
            'provident_fund'   => 'nullable|numeric',
            'telephone_bill'   => 'nullable|numeric',
            'house_rental'     => 'nullable|numeric',
            'no_pay_leave'     => 'nullable|numeric',
            'other_deductions' => 'nullable|numeric',
            // YTD fields ไม่จำเป็นต้องให้ผู้ใช้กรอก เพราะจะคำนวณอัตโนมัติ
        ]);

        // สร้าง month_year เป็น "YYYY-MM"
        $monthYear = $validated['year'] . '-' . $validated['month'];
        unset($validated['month'], $validated['year']);
        $validated['month_year'] = $monthYear;

        // รับ employee_id จาก validated data
        $empId = $validated['employee_id'];

        // คำนวณหาเดือนก่อนหน้า
        $currentYear  = (int) substr($monthYear, 0, 4);
        $currentMonth = (int) substr($monthYear, 5, 2);

        if ($currentMonth == 1) {
            $prevMonth = 12;
            $prevYear  = $currentYear - 1;
        } else {
            $prevMonth = $currentMonth - 1;
            $prevYear  = $currentYear;
        }
        $prevMonthStr = str_pad($prevMonth, 2, '0', STR_PAD_LEFT);
        $prevMonthYear = $prevYear . '-' . $prevMonthStr;

        // ค้นหา record ของเดือนก่อนหน้าสำหรับ employee เดียวกัน
        $prevPayroll = Payroll::where('employee_id', $empId)
            ->where('month_year', $prevMonthYear)
            ->first();

        // หากพบ record ในเดือนก่อน ให้ดึงค่า YTD จาก record นั้น
        $prevYtdIncome        = $prevPayroll ? $prevPayroll->ytd_income         : 0;
        $prevYtdTax           = $prevPayroll ? $prevPayroll->ytd_tax            : 0;
        $prevYtdSocialFund    = $prevPayroll ? $prevPayroll->ytd_social_fund    : 0;
        $prevYtdProvidentFund = $prevPayroll ? $prevPayroll->ytd_provident_fund : 0;

        // คำนวณ YTD ของเดือนนี้ โดยนำค่าของเดือนก่อนหน้ามาบวกกับค่าปัจจุบัน
        $validated['ytd_income']         = $prevYtdIncome        + ($validated['total_income']  ?? 0);
        $validated['ytd_tax']            = $prevYtdTax           + ($validated['tax']           ?? 0);
        $validated['ytd_social_fund']    = $prevYtdSocialFund    + ($validated['social_fund']   ?? 0);
        $validated['ytd_provident_fund'] = $prevYtdProvidentFund + ($validated['provident_fund'] ?? 0);

        // สร้าง record ใหม่ในตาราง payrolls
        Payroll::create($validated);

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll slip created successfully!');
    }

    /**
     * แสดงรายละเอียดสลิปเงินเดือน (HTML)
     */
    public function showSlip($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        $employee = $payroll->employee;

        $company = (object)[
            'name' => 'TÜV SÜD (Thailand) Limited'
        ];

        return view('payrolls.payroll-slip', compact('payroll', 'employee', 'company'));
    }

    /**
     * สร้างและดาวน์โหลดสลิปเงินเดือนเป็น PDF
     */
    public function downloadSlipPdf($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        $employee = $payroll->employee;

        $company = (object)[
            'name' => 'TÜV SÜD (Thailand) Limited'
        ];

        $pdf = Pdf::loadView('payrolls.payroll-slip', compact('payroll', 'employee', 'company'));
        $fileName = 'payroll_slip_' . ($employee->employee_code ?? $employee->id) . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * แสดงฟอร์มแก้ไขสลิปเงินเดือน
     */
    public function edit($id)
    {
        $payroll = Payroll::findOrFail($id);
        $employees = Employee::all();

        // แยกเดือน/ปี
        $monthStr = substr($payroll->month_year, 5, 2);
        $yearStr  = substr($payroll->month_year, 0, 4);

        return view('payrolls.payroll-edit', [
            'payroll' => $payroll,
            'employees' => $employees,
            'month' => $monthStr,
            'year'  => $yearStr,
        ]);
    }

    /**
     * อัปเดตสลิปเงินเดือน
     */

    public function update(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        $validated = $request->validate([
            'employee_id'             => 'required|exists:employees,id',
            'month'                   => 'required|string',
            'year'                    => 'required|digits:4',
            'salary'                  => 'nullable|numeric',
            'overtime'                => 'nullable|numeric',
            'bonus'                   => 'nullable|numeric',
            'commission'              => 'nullable|numeric',
            'transport'               => 'nullable|numeric',
            'special_severance_pay'   => 'nullable|numeric',
            'other_income'            => 'nullable|numeric',
            'tax'                     => 'nullable|numeric',
            'social_fund'             => 'nullable|numeric',
            'provident_fund'          => 'nullable|numeric',
            'telephone_bill'          => 'nullable|numeric',
            'house_rental'            => 'nullable|numeric',
            'no_pay_leave'            => 'nullable|numeric',
            'other_deductions'        => 'nullable|numeric',
            'total_income'            => 'required|numeric',
            'total_deductions'        => 'required|numeric',
            'net_income'              => 'required|numeric',
            // ฟิลด์ YTD ไม่ให้ผู้ใช้กรอก เพราะจะคำนวณอัตโนมัติ
            'accumulate_provident_fund' => 'nullable|numeric',
            'accumulate_social_fund'    => 'nullable|numeric',
            'remarks'                 => 'nullable|string',
        ]);

        // สร้าง month_year ในรูปแบบ "YYYY-MM"
        $monthYear = $validated['year'] . '-' . $validated['month'];
        unset($validated['month'], $validated['year']);
        $validated['month_year'] = $monthYear;

        // รับ employee_id ที่แก้ไข
        $empId = $validated['employee_id'];

        // คำนวณหาเดือนก่อนหน้าสำหรับเดือนปัจจุบัน
        $currentYear  = (int) substr($monthYear, 0, 4);
        $currentMonth = (int) substr($monthYear, 5, 2);

        if ($currentMonth == 1) {
            $prevMonth = 12;
            $prevYear  = $currentYear - 1;
        } else {
            $prevMonth = $currentMonth - 1;
            $prevYear  = $currentYear;
        }
        $prevMonthStr  = str_pad($prevMonth, 2, '0', STR_PAD_LEFT);
        $prevMonthYear = $prevYear . '-' . $prevMonthStr;

        // ค้นหา record ของเดือนก่อนหน้าสำหรับ employee เดียวกัน
        $prevPayroll = Payroll::where('employee_id', $empId)
            ->where('month_year', $prevMonthYear)
            ->first();

        // ถ้ามี record ของเดือนก่อนหน้า ให้ดึงค่า YTD ที่คำนวณไว้แล้ว (ถ้าไม่มีให้ใช้ 0)
        $prevYtdIncome         = $prevPayroll ? $prevPayroll->ytd_income         : 0;
        $prevYtdTax            = $prevPayroll ? $prevPayroll->ytd_tax            : 0;
        $prevYtdSocialFund     = $prevPayroll ? $prevPayroll->ytd_social_fund    : 0;
        $prevYtdProvidentFund  = $prevPayroll ? $prevPayroll->ytd_provident_fund : 0;

        // คำนวณค่า YTD สำหรับเดือนปัจจุบันโดยนำค่าของเดือนก่อนหน้ามาบวกกับค่าปัจจุบัน
        $validated['ytd_income']         = $prevYtdIncome        + ($validated['total_income']  ?? 0);
        $validated['ytd_tax']            = $prevYtdTax           + ($validated['tax']           ?? 0);
        $validated['ytd_social_fund']    = $prevYtdSocialFund    + ($validated['social_fund']   ?? 0);
        $validated['ytd_provident_fund'] = $prevYtdProvidentFund + ($validated['provident_fund'] ?? 0);

        // (หากต้องการคำนวณหรืออัปเดตฟิลด์ accumulate funds ให้ใส่ logic ที่นี่ด้วย)

        // อัปเดต record ด้วยข้อมูลที่คำนวณแล้ว
        $payroll->update($validated);

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll slip updated successfully.');
    }

    /**
     * ลบสลิปเงินเดือน
     */
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll slip deleted successfully.');
    }

    /**
     * ตรวจสอบว่ามีสลิปสำหรับ employee ในเดือน/ปีที่ระบุหรือไม่ (AJAX)
     */
    public function checkPayroll(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $month = $request->query('month');
        $year  = $request->query('year');

        $monthYear = $year . '-' . $month;

        $payroll = Payroll::where('employee_id', $employeeId)
            ->where('month_year', $monthYear)
            ->first();

        return response()->json([
            'found'   => $payroll ? true : false,
            'payroll' => $payroll,
        ]);
    }
}
