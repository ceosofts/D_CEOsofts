<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CompanyRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Company::class);

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'company_name');
        $sortDirection = $request->get('direction', 'asc');

        $query = Company::query();
        
        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('tax_id', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results, caching frequently accessed pages
        $cacheKey = "companies_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $companies = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->paginate($perPage);
        });
        
        return view('admin.companies.index', compact('companies', 'search', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new company.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Company::class);
        
        return view('admin.companies.create');
    }

    /**
     * Store a newly created company in storage.
     * 
     * @param \App\Http\Requests\CompanyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CompanyRequest $request)
    {
        $this->authorize('create', Company::class);
        
        DB::beginTransaction();
        
        try {
            $company = new Company();
            $company->fill($request->validated());
            $company->save();
            
            // Clear cache
            $this->clearCompanyCache();
            
            DB::commit();
            
            Log::info('Company created', ['id' => $company->id, 'name' => $company->company_name]);
            
            return redirect()
                ->route('admin.companies.index')
                ->with('success', 'บริษัท "' . $company->company_name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create company', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create company', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * Display the specified company.
     * 
     * @param \App\Models\Company $company
     * @return \Illuminate\View\View
     */
    public function show(Company $company)
    {
        $this->authorize('view', $company);
        
        // Load related data if needed
        // $company->load(['relatedModel']);
        
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     * 
     * @param \App\Models\Company $company
     * @return \Illuminate\View\View
     */
    public function edit(Company $company)
    {
        $this->authorize('update', $company);
        
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     * 
     * @param \App\Http\Requests\CompanyRequest $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $this->authorize('update', $company);
        
        DB::beginTransaction();
        
        try {
            $previousName = $company->company_name;
            
            $company->fill($request->validated());
            
            if ($company->isDirty()) {
                $company->save();
                
                Log::info('Company updated', [
                    'id' => $company->id,
                    'name' => $company->company_name,
                    'changes' => $company->getChanges()
                ]);
                
                // Clear cache
                $this->clearCompanyCache();
                
                $message = 'ข้อมูลบริษัท "' . $company->company_name . '" ถูกอัปเดตเรียบร้อยแล้ว';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูลบริษัท';
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.companies.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update company', [
                'id' => $company->id,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update company', [
                'id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * Remove the specified company from storage.
     * 
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);
        
        DB::beginTransaction();
        
        try {
            $companyName = $company->company_name;
            
            // Check for dependencies before deletion
            // if ($company->hasRelationships()) {
            //    return back()->withErrors(['error' => 'ไม่สามารถลบบริษัทนี้ได้ เนื่องจากมีข้อมูลที่เกี่ยวข้อง']);
            // }
            
            $company->delete();
            
            // Clear cache
            $this->clearCompanyCache();
            
            DB::commit();
            
            Log::info('Company deleted', ['id' => $company->id, 'name' => $companyName]);
            
            return redirect()
                ->route('admin.companies.index')
                ->with('success', 'บริษัท "' . $companyName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete company', [
                'id' => $company->id, 
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบบริษัทได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear company-related cache.
     * 
     * @return void
     */
    protected function clearCompanyCache(): void
    {
        // Clear cache keys that might contain company data
        Cache::flush('companies_page_*');
        Cache::forget('all_companies');
    }
    
    /**
     * Get a user-friendly database error message.
     * 
     * @param \Illuminate\Database\QueryException $exception
     * @return string
     */
    protected function getDatabaseErrorMessage(QueryException $exception): string
    {
        $errorCode = $exception->getCode();
        
        switch ($errorCode) {
            case '23000': // Integrity constraint violation
                if (strpos($exception->getMessage(), 'Duplicate entry') !== false) {
                    return 'ข้อมูลนี้มีอยู่ในระบบแล้ว กรุณาตรวจสอบข้อมูลซ้ำ';
                }
                return 'ข้อมูลขัดแย้งกับข้อมูลอื่นในระบบ';
                
            case '22001': // String data right truncation
                return 'ข้อมูลที่กรอกมีความยาวเกินกว่าที่กำหนด';
                
            default:
                return 'เกิดข้อผิดพลาดในฐานข้อมูล (รหัส: ' . $errorCode . ')';
        }
    }
}
