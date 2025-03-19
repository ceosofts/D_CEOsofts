<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\CompanyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * แสดงรายการข้อมูลบริษัททั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * แสดงฟอร์มสำหรับสร้างข้อมูลบริษัทใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * เก็บข้อมูลบริษัทที่สร้างใหม่ลงในฐานข้อมูล
     *
     * @param  \App\Http\Requests\CompanyRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        
        // จัดการกับการอัปโหลดไฟล์โลโก้ (ถ้ามี)
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }
        
        $company = Company::create($data);
        
        return redirect()->route('companies.index')
            ->with('success', 'สร้างข้อมูลบริษัทเรียบร้อยแล้ว');
    }

    /**
     * แสดงข้อมูลของบริษัทที่ระบุ
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\View\View
     */
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขข้อมูลบริษัท
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\View\View
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * อัปเดตข้อมูลบริษัทในฐานข้อมูล
     *
     * @param  \App\Http\Requests\CompanyRequest  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        
        // จัดการกับการอัปโหลดไฟล์โลโก้ (ถ้ามี)
        if ($request->hasFile('logo')) {
            // ลบไฟล์เก่า (ถ้ามี)
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            
            // บันทึกไฟล์ใหม่
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }
        
        $company->update($data);
        
        return redirect()->route('companies.index')
            ->with('success', 'อัปเดตข้อมูลบริษัทเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลบริษัทที่ระบุออกจากฐานข้อมูล
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Company $company)
    {
        // ลบไฟล์โลโก้ (ถ้ามี)
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        
        $company->delete();
        
        return redirect()->route('companies.index')
            ->with('success', 'ลบข้อมูลบริษัทเรียบร้อยแล้ว');
    }
}
