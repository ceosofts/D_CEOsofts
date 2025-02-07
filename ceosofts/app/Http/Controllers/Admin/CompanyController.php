<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * แสดงรายการบริษัททั้งหมด
     */
    public function index()
    {
        $companies = Company::paginate(10); // ✅ ใช้ paginate เพื่อแบ่งหน้าการแสดงผล
        return view('admin.companies.index', compact('companies'));
    }

    /**
     * แสดงฟอร์มสร้างบริษัทใหม่
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * บันทึกข้อมูลบริษัทใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|unique:companies|max:255',
            'branch' => 'required|integer',
            'branch_description' => 'nullable|string|max:255',
            'tax_id' => 'nullable|size:13',
            'email' => 'nullable|email',
        ]);

        try {
            $company = new Company();
            $company->fill($request->all());
            $company->save();

            return redirect()->route('admin.companies.index')->with('success', 'เพิ่มบริษัทสำเร็จ!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * แสดงข้อมูลบริษัทตาม ID
     */
    public function show(Company $company)
    {
        return view('admin.companies.show', compact('company'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลบริษัท
     */
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * อัปเดตข้อมูลบริษัท
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'company_name' => 'required|max:255|unique:companies,company_name,' . $company->id,
            'email' => 'nullable|email',
            'tax_id' => 'nullable|size:13',
        ]);

        $data = $request->except(['_token', '_method']);

        // ✅ Debug: ตรวจสอบค่าที่ส่งมาจากฟอร์ม
        Log::info('Update Request Data:', $data);

        // ✅ บังคับอัปเดตข้อมูลโดยใช้ forceFill()
        $company->forceFill($data);

        // ✅ บังคับอัปเดต timestamps (ป้องกันปัญหาที่ Laravel ไม่เห็นการเปลี่ยนแปลง)
        $company->updated_at = now();

        // ✅ Debug: ตรวจสอบค่าที่เปลี่ยนแปลงก่อน Save
        Log::info('Before Save (Dirty Data):', $company->getDirty());
        
        // ✅ บันทึกข้อมูล
        $company->save();

        // ✅ Debug: เช็ค Query ที่ถูก execute
        DB::enableQueryLog();
        Log::info('Executed Queries:', DB::getQueryLog());

        return redirect()->route('admin.companies.index')->with('success', 'อัปเดตข้อมูลบริษัทสำเร็จ!');
    }

    /**
     * ลบข้อมูลบริษัท
     */
    public function destroy(Company $company)
    {
        try {
            $company->delete();
            return redirect()->route('admin.companies.index')->with('success', 'ลบบริษัทสำเร็จ!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
