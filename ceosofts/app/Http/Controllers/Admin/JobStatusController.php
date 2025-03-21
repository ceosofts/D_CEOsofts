<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobStatus;
use App\Models\Quotation; // For dependency checks
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class JobStatusController extends Controller
{
    /**
     * Display a listing of job statuses.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', JobStatus::class);

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');

        $query = JobStatus::query();
        
        // Apply search if provided
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results, caching frequently accessed pages
        $cacheKey = "job_statuses_page_{$request->page}_{$perPage}_{$search}_{$sortField}_{$sortDirection}";
        $statuses = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query, $perPage) {
            return $query->withCount('quotations')->paginate($perPage);
        });
        
        return view('jobstatus.jobstatus-index', compact('statuses', 'search', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new job status.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', JobStatus::class);
        
        // Get highest sort order for default value
        $maxSortOrder = JobStatus::max('sort_order') ?? 0;
        
        return view('jobstatus.jobstatus-create', ['nextSortOrder' => $maxSortOrder + 10]);
    }

    /**
     * Store a newly created job status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', JobStatus::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_statuses',
            'color' => 'required|string|max:7|regex:/^#[0-9a-fA-F]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ], [
            'color.regex' => 'The color must be a valid hex color code (e.g. #FF5733).'
        ]);
        
        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        DB::beginTransaction();
        
        try {
            $jobStatus = JobStatus::create($validated);
            
            // Clear cache related to job statuses
            $this->clearJobStatusCache();
            
            DB::commit();
            
            Log::info('Job status created', [
                'id' => $jobStatus->id, 
                'name' => $jobStatus->name,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('admin.job-statuses.index')
                ->with('success', 'สถานะงาน "' . $jobStatus->name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create job status', [
                'error' => $e->getMessage(),
                'data' => $validated,
                'user_id' => auth()->id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create job status', [
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
     * Display the specified job status.
     *
     * @param  \App\Models\JobStatus  $jobStatus
     * @return \Illuminate\View\View
     */
    public function show(JobStatus $jobStatus)
    {
        $this->authorize('view', $jobStatus);
        
        // Load related quotations
        $jobStatus->load(['quotations' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('jobstatus.jobstatus-show', compact('jobStatus'));
    }

    /**
     * Show the form for editing the specified job status.
     *
     * @param  \App\Models\JobStatus  $jobStatus
     * @return \Illuminate\View\View
     */
    public function edit(JobStatus $jobStatus)
    {
        $this->authorize('update', $jobStatus);
        
        return view('jobstatus.jobstatus-edit', compact('jobStatus'));
    }

    /**
     * Update the specified job status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobStatus  $jobStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, JobStatus $jobStatus)
    {
        $this->authorize('update', $jobStatus);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_statuses,name,' . $jobStatus->id,
            'color' => 'required|string|max:7|regex:/^#[0-9a-fA-F]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ], [
            'color.regex' => 'The color must be a valid hex color code (e.g. #FF5733).'
        ]);
        
        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        DB::beginTransaction();
        
        try {
            $previousName = $jobStatus->name;
            
            // Save original values for logging changes
            $originalValues = $jobStatus->getAttributes();
            
            $jobStatus->update($validated);
            
            // Check if there were actual changes
            if ($jobStatus->wasChanged()) {
                // Log the specific changes
                $changes = [];
                foreach ($jobStatus->getChanges() as $field => $newValue) {
                    if ($field !== 'updated_at') {
                        $changes[$field] = [
                            'from' => $originalValues[$field] ?? null,
                            'to' => $newValue
                        ];
                    }
                }
                
                Log::info('Job status updated', [
                    'id' => $jobStatus->id,
                    'name' => $jobStatus->name,
                    'changes' => $changes,
                    'user_id' => auth()->id()
                ]);
                
                // Clear cache related to job statuses
                $this->clearJobStatusCache();
                
                $message = 'สถานะงาน "' . $jobStatus->name . '" ถูกอัปเดตเรียบร้อยแล้ว';
            } else {
                $message = 'ไม่มีการเปลี่ยนแปลงข้อมูล';
            }
            
            DB::commit();
            
            return redirect()->route('admin.job-statuses.index')
                ->with('success', $message);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update job status', [
                'id' => $jobStatus->id,
                'error' => $e->getMessage(),
                'data' => $validated,
                'user_id' => auth()->id()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update job status', [
                'id' => $jobStatus->id,
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
     * Remove the specified job status from storage.
     *
     * @param  \App\Models\JobStatus  $jobStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(JobStatus $jobStatus)
    {
        $this->authorize('delete', $jobStatus);
        
        DB::beginTransaction();
        
        try {
            $jobStatusName = $jobStatus->name;
            
            // Check if status is in use by any quotations
            $quotationCount = Quotation::where('status_id', $jobStatus->id)->count();
            
            if ($quotationCount > 0) {
                return back()->withErrors([
                    'error' => "ไม่สามารถลบสถานะงาน '{$jobStatusName}' ได้ เนื่องจากมีใบเสนอราคา {$quotationCount} ใบที่ใช้สถานะนี้อยู่"
                ]);
            }
            
            $jobStatus->delete();
            
            // Clear cache related to job statuses
            $this->clearJobStatusCache();
            
            DB::commit();
            
            Log::info('Job status deleted', [
                'id' => $jobStatus->id,
                'name' => $jobStatusName,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('admin.job-statuses.index')
                ->with('success', 'สถานะงาน "' . $jobStatusName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete job status', [
                'id' => $jobStatus->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบสถานะงานได้: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear job status cache.
     * 
     * @return void
     */
    protected function clearJobStatusCache(): void
    {
        Cache::flush('job_statuses_page_*');
        Cache::forget('all_job_statuses');
        Cache::forget('active_job_statuses');
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
