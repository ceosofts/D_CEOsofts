<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    protected $companyRepository;
    
    /**
     * Constructor with dependency injection
     */
    public function __construct(CompanyRepositoryInterface $companyRepository) 
    {
        $this->companyRepository = $companyRepository;
    }
    
    /**
     * แสดงรายการบริษัททั้งหมด
     */
    public function index(Request $request)
    {
        $query = Company::query();
        
        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('branch_description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }
        
        // เปลี่ยนจาก paginate() เป็น get() เพื่อให้ได้ collection เต็มสำหรับการใช้ method count() และ where()
        $companies = $query->orderBy('company_name')->get();
        
        // Debug: ดูโครงสร้างข้อมูลที่ได้
        \Log::info('Companies data for index:', ['count' => $companies->count(), 'sample' => $companies->first() ? $companies->first()->toArray() : 'No data']);
        
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
    public function store(CompanyRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->companyRepository->create($request->validated());
            DB::commit();
            
            return redirect()->route('admin.companies.index')->with('success', 'เพิ่มบริษัทสำเร็จ!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating company: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * แสดงข้อมูลบริษัทตาม ID
     */
    public function show(Company $company)
    {
        try {
            return view('admin.companies.show', compact('company'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.companies.index')->withErrors(['error' => 'ไม่พบข้อมูลบริษัท']);
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลบริษัท
     */
    public function edit(Company $company)
    {
        // โหลดข้อมูลบริษัทใหม่เพื่อป้องกันปัญหา lazy loading
        $company = Company::findOrFail($company->id);
        
        // เพิ่ม debug statement เพื่อตรวจสอบข้อมูล
        \Log::info('Company data for edit form:', ['company' => $company->toArray()]);
        
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * อัปเดตข้อมูลบริษัท
     */
    public function update(CompanyRequest $request, Company $company)
    {
        DB::beginTransaction();
        try {
            DB::enableQueryLog();
            $this->companyRepository->update($company, $request->validated());
            
            Log::info('Executed Queries:', DB::getQueryLog());
            DB::commit();
            
            return redirect()->route('admin.companies.index')->with('success', 'อัปเดตข้อมูลบริษัทสำเร็จ!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating company: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * ลบข้อมูลบริษัท
     */
    public function destroy(Company $company)
    {
        DB::beginTransaction();
        try {
            $this->companyRepository->delete($company);
            DB::commit();
            
            return redirect()->route('admin.companies.index')->with('success', 'ลบบริษัทสำเร็จ!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting company: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
