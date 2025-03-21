<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Department::class);

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'department_name');
        $sortDirection = $request->get('direction', 'asc');

        $query = Department::query();
        
        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('department_name', 'LIKE', "%{$search}%")
                  ->orWhere('department_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results, caching frequently accessed pages
        $cacheKey = "departments_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $departments = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->paginate($perPage);
        });
        
        // Get employee counts per department for display
        $employeeCounts = [];
        foreach ($departments as $department) {
            $employeeCounts[$department->id] = Employee::where('department_id', $department->id)->count();
        }
        
        return view('admin.departments.index', compact('departments', 'search', 'sortField', 'sortDirection', 'employeeCounts'));
    }

    /**
     * Show the form for creating a new department.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Department::class);
        
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department in storage.
     * 
     * @param \App\Http\Requests\DepartmentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DepartmentRequest $request)
    {
        $this->authorize('create', Department::class);
        
        DB::beginTransaction();
        
        try {
            $department = new Department();
            $department->fill($request->validated());
            
            // Generate department code if not provided
            if (empty($department->department_code)) {
                $department->department_code = $this->generateDepartmentCode($department->department_name);
            }
            
            $department->save();
            
            // Clear cache
            $this->clearDepartmentCache();
            
            DB::commit();
            
            Log::info('Department created', ['id' => $department->id, 'name' => $department->department_name]);
            
            return redirect()
                ->route('admin.departments.index')
                ->with('success', 'แผนก "' . $department->department_name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create department', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create department', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * Display the specified department.
     * 
     * @param \App\Models\Department $department
     * @return \Illuminate\View\View
     */
    public function show(Department $department)
    {
        $this->authorize('view', $department);
        
        // Load employees in this department
        $employees = Employee::where('department_id', $department->id)
            ->orderBy('name')
            ->paginate(15);
            
        return view('admin.departments.show', compact('department', 'employees'));
    }

    /**
     * Show the form for editing the specified department.
     * 
     * @param \App\Models\Department $department
     * @return \Illuminate\View\View
     */
    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     * 
     * @param \App\Http\Requests\DepartmentRequest $request
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        $this->authorize('update', $department);
        
        DB::beginTransaction();
        
        try {
            $previousName = $department->department_name;
            
            $department->fill($request->validated());
            
            // Generate department code if not provided and name changed
            if (empty($department->department_code) && $previousName != $department->department_name) {
                $department->department_code = $this->generateDepartmentCode($department->department_name);
            }
            
            if ($department->isDirty()) {
                $department->save();
                
                Log::info('Department updated', [
                    'id' => $department->id,
                    'name' => $department->department_name,
                    'changes' => $department->getChanges()
                ]);
                
                // Clear cache
                $this->clearDepartmentCache();
                
                $message = 'ข้อมูลแผนก "' . $department->department_name . '" ถูกอัปเดตเรียบร้อยแล้ว';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูลแผนก';
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.departments.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update department', [
                'id' => $department->id,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update department', [
                'id' => $department->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * Remove the specified department from storage.
     * 
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        
        DB::beginTransaction();
        
        try {
            $departmentName = $department->department_name;
            
            // Check if there are employees in this department
            $employeeCount = Employee::where('department_id', $department->id)->count();
            
            if ($employeeCount > 0) {
                return back()->withErrors([
                    'error' => 'ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีพนักงานในแผนกนี้จำนวน ' . $employeeCount . ' คน'
                ]);
            }
            
            $department->delete();
            
            // Clear cache
            $this->clearDepartmentCache();
            
            DB::commit();
            
            Log::info('Department deleted', ['id' => $department->id, 'name' => $departmentName]);
            
            return redirect()
                ->route('admin.departments.index')
                ->with('success', 'แผนก "' . $departmentName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete department', [
                'id' => $department->id, 
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบแผนกได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear department-related cache.
     * 
     * @return void
     */
    protected function clearDepartmentCache(): void
    {
        // Clear cache keys that might contain department data
        Cache::flush('departments_page_*');
        Cache::forget('all_departments');
    }
    
    /**
     * Generate a department code from department name.
     * 
     * @param string $departmentName
     * @return string
     */
    protected function generateDepartmentCode(string $departmentName): string
    {
        // Extract first letters of each word to create code
        $words = explode(' ', $departmentName);
        $code = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $code += strtoupper(mb_substr($word, 0, 1));
            }
        }
        
        // If code is too short, use first 3 letters of department name
        if (strlen($code) < 2) {
            $code = strtoupper(substr($departmentName, 0, 3));
        }
        
        // Check if code already exists, append number if needed
        $baseCode = $code;
        $counter = 1;
        
        while (Department::where('department_code', $code)->exists()) {
            $code = $baseCode + $counter;
            $counter++;
        }
        
        return $code;
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
