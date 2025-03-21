<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\TaxSettingRequest;

class TaxSettingController extends Controller
{
    /**
     * แสดงรายการการตั้งค่าภาษีทั้งหมด
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', TaxSetting::class);

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        $query = TaxSetting::query();
        
        // Apply search if provided
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('rate', 'LIKE', "%{$search}%");
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results with caching
        $cacheKey = "tax_settings_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $taxes = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->paginate($perPage);
        });
        
        return view('admin.tax.index', compact('taxes', 'search', 'sortField', 'sortDirection'));
    }

    /**
     * แสดงฟอร์มสร้างการตั้งค่าภาษีใหม่
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', TaxSetting::class);
        
        return view('admin.tax.create');
    }

    /**
     * บันทึกข้อมูลการตั้งค่าภาษีใหม่ลงในฐานข้อมูล
     * 
     * @param TaxSettingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TaxSettingRequest $request)
    {
        $this->authorize('create', TaxSetting::class);
        
        DB::beginTransaction();
        
        try {
            $taxSetting = new TaxSetting();
            $taxSetting->fill($request->validated());
            $taxSetting->save();
            
            // Clear cache
            $this->clearTaxSettingCache();
            
            DB::commit();
            
            Log::info('Tax setting created', ['id' => $taxSetting->id, 'name' => $taxSetting->name]);
            
            return redirect()->route('admin.tax.index')
                ->with('success', 'เพิ่มข้อมูลภาษี "' . $taxSetting->name . '" สำเร็จ');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing tax setting', ['error' => $e->getMessage()]);
            
            return back()->withInput()->withErrors([
                'error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * แสดงรายละเอียดการตั้งค่าภาษี
     * 
     * @param TaxSetting $tax
     * @return \Illuminate\View\View
     */
    public function show(TaxSetting $tax)
    {
        $this->authorize('view', $tax);
        
        // Load relationships if needed
        // $tax->load(['relatedModel']);
        
        return view('admin.tax.show', compact('tax'));
    }

    /**
     * แสดงฟอร์มแก้ไขการตั้งค่าภาษี
     * 
     * @param TaxSetting $tax
     * @return \Illuminate\View\View
     */
    public function edit(TaxSetting $tax)
    {
        $this->authorize('update', $tax);
        
        return view('admin.tax.edit', compact('tax'));
    }

    /**
     * อัปเดตข้อมูลการตั้งค่าภาษีในฐานข้อมูล
     * 
     * @param TaxSettingRequest $request
     * @param TaxSetting $tax
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TaxSettingRequest $request, TaxSetting $tax)
    {
        $this->authorize('update', $tax);
        
        DB::beginTransaction();
        
        try {
            $previousName = $tax->name;
            
            $tax->fill($request->validated());
            
            if ($tax->isDirty()) {
                $tax->save();
                
                Log::info('Tax setting updated', [
                    'id' => $tax->id, 
                    'name' => $tax->name,
                    'changes' => $tax->getChanges()
                ]);
                
                // Clear cache
                $this->clearTaxSettingCache();
                
                $message = 'อัปเดตข้อมูลภาษี "' . $tax->name . '" สำเร็จ';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูลภาษี';
            }
            
            DB::commit();
            
            return redirect()->route('admin.tax.index')->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tax setting', [
                'id' => $tax->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->withErrors([
                'error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ลบข้อมูลการตั้งค่าภาษี
     * 
     * @param TaxSetting $tax
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TaxSetting $tax)
    {
        $this->authorize('delete', $tax);
        
        DB::beginTransaction();
        
        try {
            $taxName = $tax->name;
            
            // Check if tax setting is in use
            $isInUse = $this->isTaxInUse($tax);
            
            if ($isInUse) {
                return back()->withErrors([
                    'error' => 'ไม่สามารถลบภาษีนี้ได้ เนื่องจากมีการใช้งานอยู่ในระบบ'
                ]);
            }
            
            $tax->delete();
            
            // Clear cache
            $this->clearTaxSettingCache();
            
            DB::commit();
            
            Log::info('Tax setting deleted', ['id' => $tax->id, 'name' => $taxName]);
            
            return redirect()->route('admin.tax.index')
                ->with('success', 'ลบข้อมูลภาษี "' . $taxName . '" สำเร็จ');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting tax setting', [
                'id' => $tax->id, 
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบข้อมูลภาษีได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear tax setting cache.
     * 
     * @return void
     */
    protected function clearTaxSettingCache(): void
    {
        // Clear cache keys that might contain tax setting data
        Cache::flush('tax_settings_page_*');
        Cache::forget('all_tax_settings');
    }
    
    /**
     * Check if tax setting is in use.
     * 
     * @param TaxSetting $tax
     * @return bool
     */
    protected function isTaxInUse(TaxSetting $tax): bool
    {
        // Check relationships where tax setting might be used
        // Example:
        // return Invoice::where('tax_setting_id', $tax->id)->exists() || 
        //        Product::where('tax_setting_id', $tax->id)->exists();
        
        // For now, return false to allow deletion
        // Replace with actual implementation based on your data model
        return false;
    }
}
