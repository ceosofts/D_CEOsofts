<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class PositionController extends Controller
{
    /**
     * แสดงรายการตำแหน่งทั้งหมด
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Position::class);
        
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $query = Position::query();
        
        // Apply search if provided
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results with employee counts
        $cacheKey = "positions_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $positions = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->withCount('employees')->paginate($perPage);
        });
        
        // Get employee count summary (for display in view)
        $employeeCountByPosition = [];
        foreach ($positions as $position) {
            $employeeCountByPosition[$position->id] = $position->employees_count;
        }
        
        return view('admin.positions.index', compact(
            'positions', 
            'search', 
            'sortField', 
            'sortDirection',
            'employeeCountByPosition'
        ));
    }

    /**
     * แสดงฟอร์มสร้างตำแหน่งใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Position::class);
        
        return view('admin.positions.create');
    }

    /**
     * บันทึกตำแหน่งใหม่ลงในฐานข้อมูล
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Position::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'description' => 'nullable|string|max:1000',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0|gte:salary_range_min',
            'is_active' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $position = new Position();
            $position->fill($validated);
            $position->is_active = $request->has('is_active');
            $position->save();
            
            // Clear position cache
            $this->clearPositionCache();
            
            DB::commit();
            
            Log::info('Position created', ['id' => $position->id, 'name' => $position->name]);
            
            return redirect()->route('admin.positions.index')
                ->with('success', 'ตำแหน่ง "' . $position->name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create position', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create position', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * แสดงรายละเอียดตำแหน่งและพนักงานที่มีตำแหน่งนี้
     * 
     * @param Position $position
     * @return \Illuminate\View\View
     */
    public function show(Position $position)
    {
        $this->authorize('view', $position);
        
        // Get employees with this position
        $employees = Employee::with(['department'])
            ->where('position_id', $position->id)
            ->orderBy('name')
            ->paginate(15);
            
        return view('admin.positions.show', compact('position', 'employees'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลตำแหน่ง
     * 
     * @param Position $position
     * @return \Illuminate\View\View
     */
    public function edit(Position $position)
    {
        $this->authorize('update', $position);
        
        return view('admin.positions.edit', compact('position'));
    }

    /**
     * อัปเดตข้อมูลตำแหน่งในฐานข้อมูล
     *
     * @param Request $request
     * @param Position $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Position $position)
    {
        $this->authorize('update', $position);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'description' => 'nullable|string|max:1000',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0|gte:salary_range_min',
            'is_active' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Track what has changed
            $originalPosition = $position->getAttributes();
            
            $position->fill($validated);
            $position->is_active = $request->has('is_active');
            
            if ($position->isDirty()) {
                $position->save();
                
                Log::info('Position updated', [
                    'id' => $position->id,
                    'name' => $position->name,
                    'changes' => $position->getChanges()
                ]);
                
                // Clear cache
                $this->clearPositionCache();
                
                $message = 'ตำแหน่ง "' . $position->name . '" ถูกอัปเดตเรียบร้อยแล้ว';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูล';
            }
            
            DB::commit();
            
            return redirect()->route('admin.positions.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update position', [
                'id' => $position->id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update position', [
                'id' => $position->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * ลบข้อมูลตำแหน่ง
     *
     * @param Position $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Position $position)
    {
        $this->authorize('delete', $position);
        
        DB::beginTransaction();
        
        try {
            $positionName = $position->name;
            
            // Check if there are employees with this position
            $employeeCount = Employee::where('position_id', $position->id)->count();
            
            if ($employeeCount > 0) {
                return back()->withErrors([
                    'error' => 'ไม่สามารถลบตำแหน่งนี้ได้ เนื่องจากมีพนักงานที่มีตำแหน่งนี้จำนวน ' . $employeeCount . ' คน'
                ]);
            }
            
            $position->delete();
            
            // Clear cache
            $this->clearPositionCache();
            
            DB::commit();
            
            Log::info('Position deleted', ['id' => $position->id, 'name' => $positionName]);
            
            return redirect()->route('admin.positions.index')
                ->with('success', 'ตำแหน่ง "' . $positionName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete position', [
                'id' => $position->id, 
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบตำแหน่งได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * เปลี่ยนสถานะการใช้งานของตำแหน่ง
     *
     * @param Position $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(Position $position)
    {
        $this->authorize('update', $position);
        
        DB::beginTransaction();
        
        try {
            $position->is_active = !$position->is_active;
            $position->save();
            
            // Clear cache
            $this->clearPositionCache();
            
            $status = $position->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            
            DB::commit();
            
            Log::info('Position status toggled', [
                'id' => $position->id,
                'name' => $position->name,
                'is_active' => $position->is_active
            ]);
            
            return back()->with('success', "ตำแหน่ง \"$position->name\" ถูก{$status}เรียบร้อยแล้ว");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle position status', [
                'id' => $position->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถเปลี่ยนสถานะการใช้งานของตำแหน่งได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * จัดการการทำงานกับตำแหน่งหลายรายการพร้อมกัน
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $this->authorize('update-any', Position::class);
        
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:positions,id',
            'action' => 'required|in:activate,deactivate,delete'
        ]);
        
        $action = $validated['action'];
        $ids = $validated['ids'];
        $count = count($ids);
        
        DB::beginTransaction();
        
        try {
            if ($action === 'delete') {
                // Check if any of the positions has employees
                $positionsWithEmployees = Position::whereIn('id', $ids)
                    ->whereHas('employees')
                    ->pluck('name')
                    ->toArray();
                
                if (!empty($positionsWithEmployees)) {
                    return back()->withErrors([
                        'error' => 'ไม่สามารถลบตำแหน่งบางรายการได้ เนื่องจากมีพนักงานที่มีตำแหน่งนี้: ' . implode(', ', $positionsWithEmployees)
                    ]);
                }
                
                Position::destroy($ids);
                $message = "ลบตำแหน่งจำนวน {$count} รายการเรียบร้อยแล้ว";
            } else {
                $isActive = ($action === 'activate');
                Position::whereIn('id', $ids)->update(['is_active' => $isActive]);
                
                $status = $isActive ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                $message = "{$status}ตำแหน่งจำนวน {$count} รายการเรียบร้อยแล้ว";
            }
            
            // Clear cache
            $this->clearPositionCache();
            
            DB::commit();
            
            Log::info('Bulk position action', [
                'action' => $action,
                'ids' => $ids,
                'count' => $count
            ]);
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to perform bulk action on positions', [
                'action' => $action,
                'ids' => $ids,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถดำเนินการกับตำแหน่งที่เลือกได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * ล้างข้อมูล cache ที่เกี่ยวข้องกับตำแหน่ง
     *
     * @return void
     */
    protected function clearPositionCache(): void
    {
        Cache::flush('positions_page_*');
        Cache::forget('all_positions');
        Cache::forget('active_positions');
    }
    
    /**
     * Get a user-friendly database error message
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
