<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class UnitController extends Controller
{
    /**
     * แสดงรายการหน่วยสินค้า
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Unit::class);

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $query = Unit::query();
        
        // Apply search if provided
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results with caching
        $cacheKey = "units_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $units = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->withCount('products')->paginate($perPage);
        });
        
        return view('admin.units.index', compact('units', 'search', 'sortField', 'sortDirection'));
    }

    /**
     * แสดงฟอร์มสร้างหน่วยสินค้าใหม่
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Unit::class);
        
        return view('admin.units.create');
    }

    /**
     * บันทึกหน่วยสินค้าใหม่ลงในฐานข้อมูล
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Unit::class);
        
        $validated = $request->validate([
            'name' => 'required|unique:units,name|max:255',
            'description' => 'nullable|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            $unit = new Unit();
            $unit->fill($validated);
            $unit->save();
            
            // Clear unit cache
            $this->clearUnitCache();
            
            DB::commit();
            
            Log::info('Unit created', ['id' => $unit->id, 'name' => $unit->name]);
            
            return redirect()->route('admin.units.index')
                ->with('success', 'หน่วยสินค้า "' . $unit->name . '" เพิ่มสำเร็จ');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create unit', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create unit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * แสดงรายละเอียดหน่วยสินค้า
     * 
     * @param Unit $unit
     * @return \Illuminate\View\View
     */
    public function show(Unit $unit)
    {
        $this->authorize('view', $unit);
        
        // Load products associated with this unit
        $products = Product::where('unit_id', $unit->id)
            ->orderBy('name')
            ->paginate(10);
            
        return view('admin.units.show', compact('unit', 'products'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลหน่วยสินค้า
     * 
     * @param Unit $unit
     * @return \Illuminate\View\View
     */
    public function edit(Unit $unit)
    {
        $this->authorize('update', $unit);
        
        return view('admin.units.edit', compact('unit'));
    }

    /**
     * อัปเดตข้อมูลหน่วยสินค้าในฐานข้อมูล
     * 
     * @param Request $request
     * @param Unit $unit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Unit $unit)
    {
        $this->authorize('update', $unit);
        
        $validated = $request->validate([
            'name' => 'required|unique:units,name,' . $unit->id . '|max:255',
            'description' => 'nullable|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            $previousName = $unit->name;
            
            $unit->fill($validated);
            
            if ($unit->isDirty()) {
                $unit->save();
                
                Log::info('Unit updated', [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'changes' => $unit->getChanges()
                ]);
                
                // Clear cache
                $this->clearUnitCache();
                
                $message = 'อัปเดตหน่วยสินค้า "' . $unit->name . '" สำเร็จ';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูลหน่วยสินค้า';
            }
            
            DB::commit();
            
            return redirect()->route('admin.units.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update unit', [
                'id' => $unit->id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update unit', [
                'id' => $unit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * ลบข้อมูลหน่วยสินค้า
     * 
     * @param Unit $unit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Unit $unit)
    {
        $this->authorize('delete', $unit);
        
        DB::beginTransaction();
        
        try {
            $unitName = $unit->name;
            
            // Check if unit is associated with products
            $productCount = Product::where('unit_id', $unit->id)->count();
            
            if ($productCount > 0) {
                return back()->withErrors([
                    'error' => 'ไม่สามารถลบหน่วยสินค้านี้ได้ เนื่องจากมีสินค้าที่ใช้หน่วยนี้จำนวน ' . $productCount . ' รายการ'
                ]);
            }
            
            $unit->delete();
            
            // Clear cache
            $this->clearUnitCache();
            
            DB::commit();
            
            Log::info('Unit deleted', ['id' => $unit->id, 'name' => $unitName]);
            
            return redirect()->route('admin.units.index')
                ->with('success', 'ลบหน่วยสินค้า "' . $unitName . '" สำเร็จ');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete unit', [
                'id' => $unit->id, 
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบหน่วยสินค้าได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear unit-related cache.
     * 
     * @return void
     */
    protected function clearUnitCache(): void
    {
        // Clear cache keys that might contain unit data
        Cache::flush('units_page_*');
        Cache::forget('all_units');
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
