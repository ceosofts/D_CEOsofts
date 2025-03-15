<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Wage; // ถ้ามี Model Wage สำหรับดึงข้อมูลค่าแรง
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\ThaiPdfService;
use App\Models\Company;  // เพิ่ม use statement

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

        // กรองด้วยข้อความค้นหา (ค้นหาจาก employee_code, first_name, last_name)
        if (!empty($search)) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_code', 'LIKE', "%$search%")
                    ->orWhere('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%");
            });
        }

        // กรองโดยเดือน/ปี หากมีค่า
        if (!empty($month) && !empty($year)) {
            $monthYear = $year . '-' . $month;
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
        $employees = Employee::all();

        // รับค่า employee_id, month, year จาก query string (เพื่อ auto-fill ข้อมูลจากตาราง wage)
        $empId = $request->query('employee_id');
        $month = $request->query('month');
        $year  = $request->query('year');

        // กำหนดค่า default สำหรับฟิลด์ต่างๆ
        $autoFill = [
            'salary'                => 0,
            'overtime'              => 0,
            'bonus'                 => 0,
            'commission'            => 0,
            'transport'             => 0,
            'special_severance_pay' => 0,
            'other_income'          => 0,
            'tax'                   => 0,
            'social_fund'           => 0,
            'provident_fund'        => 0,
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

        // หากมี employee, month, year ให้ดึงข้อมูลจาก Wage (ถ้ามี)
        if ($empId && $month && $year) {
            $monthYear = $year . '-' . $month;
            $wage = Wage::where('employee_id', $empId)
                ->where('month_year', $monthYear)
                ->first();
            if ($wage) {
                // Mapping ค่า จาก Wage มายัง autoFill (ปรับตามที่ต้องการ)
                $autoFill['salary'] = $wage->total_wage;
                $autoFill['overtime'] = $wage->ot_pay;
                $autoFill['bonus'] = $wage->bonus;
                $autoFill['commission'] = $wage->commission;
                $autoFill['transport'] = $wage->transport;
                $autoFill['special_severance_pay'] = $wage->special_severance_pay;
                $autoFill['other_income'] = $wage->other_income;
                $autoFill['total_income'] = $wage->grand_total;
                // สมมติให้ provident_fund เป็นค่า accumulate_provident_fund
                $autoFill['accumulate_provident_fund'] = $wage->provident_fund ?? 0;
                // เพิ่มเติม mapping อื่น ๆ ตามที่ต้องการ
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
            'tax'              => 'nullable|numeric',
            'social_fund'      => 'nullable|numeric',
            'provident_fund'   => 'nullable|numeric',
            'telephone_bill'   => 'nullable|numeric',
            'house_rental'     => 'nullable|numeric',
            'no_pay_leave'     => 'nullable|numeric',
            'other_deductions' => 'nullable|numeric',
            'total_income'     => 'required|numeric',
            'total_deductions' => 'required|numeric',
            'net_income'       => 'required|numeric',
        ]);

        // สร้าง month_year ในรูปแบบ "YYYY-MM"
        $monthYear = $validated['year'] . '-' . $validated['month'];
        unset($validated['month'], $validated['year']);
        $validated['month_year'] = $monthYear;

        // *** ย้ายการคำนวณ Net Income มาทำก่อน validate ***
        $totalIncome = floatval($request->input('total_income', 0));
        $totalDeductions = floatval($request->input('total_deductions', 0));
        $netIncome = $totalIncome - $totalDeductions;

        $validated['total_income'] = $totalIncome;
        $validated['total_deductions'] = $totalDeductions;
        $validated['net_income'] = $netIncome;

        // รับ employee_id จาก validated data
        $empId = $validated['employee_id'];

        // แยกปีและเดือนจาก month_year
        list($currentYear, $currentMonth) = array_map('intval', explode('-', $monthYear));

        // คำนวณหาเดือนก่อนหน้า
        if ($currentMonth == 1) {
            $prevMonth = 12;
            $prevYear  = $currentYear - 1;
        } else {
            $prevMonth = $currentMonth - 1;
            $prevYear  = $currentYear;
        }
        $prevMonthStr  = str_pad($prevMonth, 2, '0', STR_PAD_LEFT);
        $prevMonthYear = $prevYear . '-' . $prevMonthStr;

        // ค้นหา record ของ payroll สำหรับ employee เดียวกันในเดือนก่อนหน้า
        $prevPayroll = Payroll::where('employee_id', $empId)
            ->where('month_year', $prevMonthYear)
            ->first();

        // ดึงค่า YTD จากเดือนก่อนหน้า (ถ้าไม่มีให้ใช้ 0)
        $prevYtdIncome         = $prevPayroll ? $prevPayroll->ytd_income : 0;
        $prevYtdTax            = $prevPayroll ? $prevPayroll->ytd_tax : 0;
        $prevYtdSocialFund     = $prevPayroll ? $prevPayroll->ytd_social_fund : 0;
        $prevYtdProvidentFund  = $prevPayroll ? $prevPayroll->ytd_provident_fund : 0;

        // คำนวณค่า YTD สำหรับเดือนนี้
        $validated['ytd_income']         = $prevYtdIncome + ($validated['total_income'] ?? 0);
        $validated['ytd_tax']            = $prevYtdTax + ($validated['tax'] ?? 0);
        $validated['ytd_social_fund']    = $prevYtdSocialFund + ($validated['social_fund'] ?? 0);
        $validated['ytd_provident_fund'] = $prevYtdProvidentFund + ($validated['provident_fund'] ?? 0);

        $validated['created_by'] = auth()->id();
        
        // สร้าง record ในตาราง payrolls
        Payroll::create($validated);

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll slip created successfully!');
    }

    /**
     * แสดงรายละเอียดสลิปเงินเดือน (HTML)
     */
    public function showSlip($id)
    {
        // ดึงข้อมูล payroll และ employee
        $payroll = Payroll::with('employee')->findOrFail($id);
        $employee = $payroll->employee;
        
        // ดึงข้อมูลบริษัทจากฐานข้อมูล
        $company = Company::first();
        
        if (!$company) {
            // ถ้าไม่พบข้อมูลบริษัท ให้ใช้ค่าเริ่มต้น
            $company = (object)[
                'name' => 'Company Name Not Found'
            ];
        } else {
            // แปลงข้อมูลให้ตรงกับที่ใช้ในเทมเพลต
            $company = (object)[
                'name' => $company->company_name
            ];
        }

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
            'name' => 'TÜV SÜD (Thailand) Limited',
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
        $monthStr = substr($payroll->month_year, 5, 2);
        $yearStr  = substr($payroll->month_year, 0, 4);

        return view('payrolls.payroll-edit', [
            'payroll'   => $payroll,
            'employees' => $employees,
            'month'     => $monthStr,
            'year'      => $yearStr,
        ]);
    }

    /**
     * อัปเดตสลิปเงินเดือน
     */
    public function update(Request $request, $id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            
            // dump request data for debugging
            \Log::info('Update Payroll Request:', $request->all());

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
                'remarks'                 => 'nullable|string|max:1000',
            ]);

            // สร้าง month_year เป็น "YYYY-MM"
            $monthYear = $validated['year'] . '-' . $validated['month'];
            unset($validated['month'], $validated['year']);
            $validated['month_year'] = $monthYear;

            // *** ย้ายการคำนวณ Net Income มาทำก่อน validate ***
            $totalIncome = floatval($request->input('total_income', 0));
            $totalDeductions = floatval($request->input('total_deductions', 0));
            $netIncome = $totalIncome - $totalDeductions;

            $validated['total_income'] = $totalIncome;
            $validated['total_deductions'] = $totalDeductions;
            $validated['net_income'] = $netIncome;

            $empId = $validated['employee_id'];

            list($currentYear, $currentMonth) = array_map('intval', explode('-', $monthYear));
            if ($currentMonth == 1) {
                $prevMonth = 12;
                $prevYear  = $currentYear - 1;
            } else {
                $prevMonth = $currentMonth - 1;
                $prevYear  = $currentYear;
            }
            $prevMonthStr  = str_pad($prevMonth, 2, '0', STR_PAD_LEFT);
            $prevMonthYear = $prevYear . '-' . $prevMonthStr;

            $prevPayroll = Payroll::where('employee_id', $empId)
                ->where('month_year', $prevMonthYear)
                ->first();

            $prevYtdIncome         = $prevPayroll ? $prevPayroll->ytd_income : 0;
            $prevYtdTax            = $prevPayroll ? $prevPayroll->ytd_tax : 0;
            $prevYtdSocialFund     = $prevPayroll ? $prevPayroll->ytd_social_fund : 0;
            $prevYtdProvidentFund  = $prevPayroll ? $prevPayroll->ytd_provident_fund : 0;

            $validated['ytd_income']         = $prevYtdIncome + ($validated['total_income'] ?? 0);
            $validated['ytd_tax']            = $prevYtdTax + ($validated['tax'] ?? 0);
            $validated['ytd_social_fund']    = $prevYtdSocialFund + ($validated['social_fund'] ?? 0);
            $validated['ytd_provident_fund'] = $prevYtdProvidentFund + ($validated['provident_fund'] ?? 0);

            \DB::beginTransaction();

            // ตรวจสอบและเพิ่ม created_by ถ้าไม่มี
            if (empty($payroll->created_by)) {
                $payroll->created_by = auth()->id();
                $payroll->save();
            }

            // เพิ่ม updated_by ในข้อมูลที่จะอัพเดท
            $validated['updated_by'] = auth()->id();
            
            // Log before update
            \Log::info('Before Update:', ['validated' => $validated]);
            
            // อัพเดทข้อมูล
            $payroll->update($validated);
            
            \DB::commit();

            // Log after update
            \Log::info('Update successful for payroll ID: ' . $id);

            return redirect()->route('payroll.index')
                ->with('success', 'Payroll slip updated successfully.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating payroll: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()
                ->withInput()
                ->with('error', 'Failed to update payroll: ' . $e->getMessage());
        }
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

    public function generatePDF($id)
    {
        try {
            // ดึงข้อมูล payroll
            $payroll = Payroll::with('employee')->findOrFail($id);
            
            // ดึงข้อมูลบริษัทจากฐานข้อมูล
            $company = Company::first();
            
            if (!$company) {
                // ถ้าไม่พบข้อมูลบริษัท ให้ใช้ค่าเริ่มต้น
                $company = (object)[
                    'name' => 'Company Name Not Found'
                ];
            } else {
                // แปลงข้อมูลให้ตรงกับที่ใช้ในเทมเพลต
                $company = (object)[
                    'name' => $company->company_name
                ];
            }

            // สร้าง HTML content
            $html = view('payrolls.payroll-pdf', [
                'payroll' => $payroll,
                'company' => $company
            ])->render();

            // ใช้ ThaiPdfService เพื่อสร้าง PDF
            $pdfService = new ThaiPdfService();
            return $pdfService->generatePdf($html, [
                'filename' => 'payroll_' . $payroll->employee->employee_code . '_' . 
                            $payroll->month_year . '.pdf'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return back()->with('error', 'ไม่สามารถสร้างไฟล์ PDF ได้: ' . $e->getMessage());
        }
    }
}
