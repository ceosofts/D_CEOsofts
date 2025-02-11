<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyHoliday;

class CompanyHolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y')); // ค่าเริ่มต้นเป็นปีปัจจุบัน

        $holidays = CompanyHoliday::whereRaw("strftime('%Y', date) = ?", [$year])
                    ->orderBy('date', 'asc')
                    ->paginate(20);


        $years = CompanyHoliday::selectRaw("strftime('%Y', date) as year")
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');


        return view('company_holidays.index', compact('holidays', 'years', 'year'));
    }


    public function create()
    {
        return view('company_holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:company_holidays,date',
            'name' => 'required|string|max:255'
        ]);

        CompanyHoliday::create($request->all());

        return redirect()->route('company-holidays.index')->with('success', 'เพิ่มวันหยุดสำเร็จ!');
    }

    public function edit(CompanyHoliday $companyHoliday)
    {
        return view('company_holidays.edit', compact('companyHoliday'));
    }

    public function update(Request $request, CompanyHoliday $companyHoliday)
    {
        $request->validate([
            'date' => 'required|date|unique:company_holidays,date,' . $companyHoliday->id,
            'name' => 'required|string|max:255'
        ]);

        $companyHoliday->update($request->all());

        return redirect()->route('company-holidays.index')->with('success', 'อัปเดตวันหยุดสำเร็จ!');
    }

    public function destroy(CompanyHoliday $companyHoliday)
    {
        $companyHoliday->delete();

        return redirect()->route('company-holidays.index')->with('success', 'ลบวันหยุดสำเร็จ!');
    }

public function __construct()
{
    $this->middleware('role:admin,manager')->only(['index']);
    $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
}



}
