<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prefix;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class PrefixController extends Controller
{
    /**
     * แสดงรายการคำนำหน้าชื่อทั้งหมด
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Prefix::class);
        
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $query = Prefix::query();
        
        // Apply search if provided
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Cache frequently accessed pages to improve performance
        $cacheKey = "prefixes_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $prefixes = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->paginate($perPage);
        });
        
        return view('admin.prefixes.index', compact('prefixes', 'search', 'sortField', 'sortDirection'));
    }

    /**
     * แสดงฟอร์มสร้างคำนำหน้าชื่อใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Prefix::class);
        
        return view('admin.prefixes.create');
    }

    /**
     * บันทึกข้อมูลคำนำหน้าชื่อใหม่ลงในฐานข้อมูล
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Prefix::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:prefixes,name',
            'is_active' => 'sometimes|boolean',
        ]);
        
        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        DB::beginTransaction();
        
        try {
            $prefix = Prefix::create($validated);
            
            // Clear prefix cache
            $this->clearPrefixCache();
            
            DB::commit();
            
            Log::info('Prefix created', [
                'id' => $prefix->id, 
                'name' => $prefix->name,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('admin.prefixes.index')
                ->with('success', 'คำนำหน้า "' . $prefix->name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create prefix', [
                'error' => $e->getMessage(),
                'data' => $validated,
                'user_id' => auth()->id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create prefix', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * แสดงข้อมูลคำนำหน้าชื่อ
     * 
     * @param Prefix $prefix
     * @return \Illuminate\View\View
     */
    public function show(Prefix $prefix)
    {
        $this->authorize('view', $prefix);
        
        // Check cache for prefix usage stats
        $cacheKey = "prefix_usage_{$prefix->id}";
        $stats = Cache::remember($cacheKey, now()->addHours(3), function() use ($prefix) {
            // Here you would get usage statistics
            // For example: count of employees, customers using this prefix
            return [
                'employee_count' => DB::table('employees')->where('prefix_id', $prefix->id)->count(),
                'customer_count' => DB::table('customers')->where('prefix_id', $prefix->id)->count(),
            ];
        });
        
        return view('admin.prefixes.show', compact('prefix', 'stats'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลคำนำหน้าชื่อ
     * 
     * @param Prefix $prefix
     * @return \Illuminate\View\View
     */
    public function edit(Prefix $prefix)
    {
        $this->authorize('update', $prefix);
        
        return view('admin.prefixes.edit', compact('prefix'));
    }

    /**
     * อัปเดตข้อมูลคำนำหน้าชื่อในฐานข้อมูล
     * 
     * @param Request $request
     * @param Prefix $prefix
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Prefix $prefix)
    {
        $this->authorize('update', $prefix);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:prefixes,name,' . $prefix->id,
            'is_active' => 'sometimes|boolean',
        ]);
        
        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        DB::beginTransaction();
        
        try {
            $previousName = $prefix->name;
            
            // Save original values for logging changes
            $originalValues = $prefix->getAttributes();
            
            $prefix->update($validated);
            
            // Check if there were actual changes
            if ($prefix->wasChanged()) {
                // Log the specific changes
                $changes = [];
                foreach ($prefix->getChanges() as $field => $newValue) {
                    if ($field !== 'updated_at') {
                        $changes[$field] = [
                            'from' => $originalValues[$field] ?? null,
                            'to' => $newValue
                        ];
                    }
                }
                
                Log::info('Prefix updated', [
                    'id' => $prefix->id,
                    'name' => $prefix->name,
                    'changes' => $changes,
                    'user_id' => auth()->id()
                ]);
                
                // Clear cache related to prefixes
                $this->clearPrefixCache();
                
                $message = 'คำนำหน้า "' . $prefix->name . '" ถูกอัปเดตเรียบร้อยแล้ว';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูล';
            }
            
            DB::commit();
            
            return redirect()->route('admin.prefixes.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update prefix', [
                'id' => $prefix->id,
                'error' => $e->getMessage(),
                'data' => $validated,
                'user_id' => auth()->id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update prefix', [
                'id' => $prefix->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * ลบข้อมูลคำนำหน้าชื่อ
     * 
     * @param Prefix $prefix
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Prefix $prefix)
    {
        $this->authorize('delete', $prefix);
        
        DB::beginTransaction();
        
        try {
            $prefixName = $prefix->name;
            
            // Check if prefix is in use by any employees or customers
            $employeeCount = DB::table('employees')->where('prefix_id', $prefix->id)->count();
            $customerCount = DB::table('customers')->where('prefix_id', $prefix->id)->count();
            
            $totalUsage = $employeeCount + $customerCount;
            
            if ($totalUsage > 0) {
                $usageDetail = [];
                if ($employeeCount > 0) $usageDetail[] = "พนักงาน {$employeeCount} คน";
                if ($customerCount > 0) $usageDetail[] = "ลูกค้า {$customerCount} คน";
                
                $usageMessage = implode(' และ ', $usageDetail);
                
                return back()->withErrors([
                    'error' => "ไม่สามารถลบคำนำหน้า '{$prefixName}' ได้ เนื่องจากมี{$usageMessage}ที่ใช้คำนำหน้านี้"
                ]);
            }
            
            $prefix->delete();
            
            // Clear cache related to prefixes
            $this->clearPrefixCache();
            
            DB::commit();
            
            Log::info('Prefix deleted', [
                'id' => $prefix->id,
                'name' => $prefixName,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('admin.prefixes.index')
                ->with('success', 'คำนำหน้า "' . $prefixName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete prefix', [
                'id' => $prefix->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบคำนำหน้าได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * เปลี่ยนสถานะการใช้งานของคำนำหน้า
     * 
     * @param Prefix $prefix
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(Prefix $prefix)
    {
        $this->authorize('update', $prefix);
        
        DB::beginTransaction();
        
        try {
            $prefix->is_active = !$prefix->is_active;
            $prefix->save();
            
            // Clear cache
            $this->clearPrefixCache();
            
            $status = $prefix->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            
            DB::commit();
            
            Log::info('Prefix status toggled', [
                'id' => $prefix->id,
                'name' => $prefix->name,
                'is_active' => $prefix->is_active,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('success', "คำนำหน้า \"{$prefix->name}\" ถูก{$status}เรียบร้อยแล้ว");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle prefix status', [
                'id' => $prefix->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถเปลี่ยนสถานะการใช้งานได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * ดำเนินการกับคำนำหน้าหลายรายการพร้อมกัน
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $this->authorize('update-any', Prefix::class);
        
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:prefixes,id',
            'action' => 'required|in:activate,deactivate,delete'
        ]);
        
        $action = $validated['action'];
        $ids = $validated['ids'];
        $count = count($ids);
        
        DB::beginTransaction();
        
        try {
            if ($action === 'delete') {
                // Check if any of the prefixes are in use
                $employeeCount = DB::table('employees')->whereIn('prefix_id', $ids)->count();
                $customerCount = DB::table('customers')->whereIn('prefix_id', $ids)->count();
                
                if ($employeeCount > 0 || $customerCount > 0) {
                    $usageDetail = [];
                    if ($employeeCount > 0) $usageDetail[] = "{$employeeCount} พนักงาน";
                    if ($customerCount > 0) $usageDetail[] = "{$customerCount} ลูกค้า";
                    
                    return back()->withErrors([
                        'error' => 'ไม่สามารถลบคำนำหน้าบางรายการได้ เนื่องจากมีการใช้งานโดย ' . implode(' และ ', $usageDetail)
                    ]);
                }
                
                Prefix::destroy($ids);
                $message = "ลบคำนำหน้าจำนวน {$count} รายการเรียบร้อยแล้ว";
            } else {
                $isActive = ($action === 'activate');
                Prefix::whereIn('id', $ids)->update(['is_active' => $isActive]);
                
                $status = $isActive ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                $message = "{$status}คำนำหน้าจำนวน {$count} รายการเรียบร้อยแล้ว";
            }
            
            // Clear cache
            $this->clearPrefixCache();
            
            DB::commit();
            
            Log::info('Bulk prefix action', [
                'action' => $action,
                'ids' => $ids,
                'count' => $count,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to perform bulk action on prefixes', [
                'action' => $action,
                'ids' => $ids,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถดำเนินการกับคำนำหน้าที่เลือกได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear prefix cache.
     * 
     * @return void
     */
    protected function clearPrefixCache(): void
    {
        Cache::flush('prefixes_page_*');
        Cache::forget('all_prefixes');
        Cache::forget('active_prefixes');
        
        // Clear individual prefix usage caches
        $prefixIds = Prefix::pluck('id');
        foreach ($prefixIds as $id) {
            Cache::forget("prefix_usage_{$id}");
        }
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
