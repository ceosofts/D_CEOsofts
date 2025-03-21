<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * แสดงรายการผู้ใช้ระบบ
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', User::class);

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $role = $request->get('role');

        $query = User::query();
        
        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by role if provided
        if ($role) {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results with eager loading
        $users = $query->with(['roles', 'department'])->paginate($perPage);
        
        // Get all available roles for filter dropdown
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'search', 'sortField', 'sortDirection', 'role', 'roles'));
    }

    /**
     * แสดงฟอร์มสร้างผู้ใช้ใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        // ดึง Role ทั้งหมด
        $roles = Role::all();
        // ดึงข้อมูลแผนกทั้งหมด
        $departments = Department::orderBy('department_name')->get();

        return view('admin.users.create', compact('roles', 'departments'));
    }

    /**
     * บันทึกผู้ใช้ใหม่ลงในฐานข้อมูล
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required', 
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
            'role' => 'required|exists:roles,name',
            'department_id' => 'nullable|exists:departments,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        
        try {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->department_id = $validated['department_id'] ?? null;
            
            // Handle profile image upload if provided
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $imagePath;
            }
            
            $user->save();

            // Assign role
            $user->assignRole($validated['role']);
            
            DB::commit();
            
            Log::info('User created', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $validated['role']
            ]);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'ผู้ใช้ "' . $user->name . '" ถูกเพิ่มเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * แสดงรายละเอียดผู้ใช้
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        // Load relationships
        $user->load(['roles', 'department']);
        
        // Get user activity if available
        $recentActivity = [];
        if (method_exists($user, 'activities')) {
            $recentActivity = $user->activities()->latest()->take(10)->get();
        }
        
        return view('admin.users.show', compact('user', 'recentActivity'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลผู้ใช้
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $roles = Role::all();
        $departments = Department::orderBy('department_name')->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * อัปเดตข้อมูลผู้ใช้ในฐานข้อมูล
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'department_id' => 'nullable|exists:departments,id',
            'password' => [
                'nullable', 
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        
        try {
            $previousEmail = $user->email;
            
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->department_id = $validated['department_id'] ?? null;
            
            // Update password if provided
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            
            // Handle profile image upload if provided
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    if (file_exists(storage_path('app/public/' . $user->profile_image))) {
                        unlink(storage_path('app/public/' . $user->profile_image));
                    }
                }
                
                $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $imagePath;
            }
            
            if ($user->isDirty()) {
                $user->save();
                
                Log::info('User updated', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'changes' => $user->getChanges()
                ]);
            }
            
            // Update user role
            if (!$user->hasRole($validated['role'])) {
                $user->syncRoles([$validated['role']]);
                
                Log::info('User role updated', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $validated['role']
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'ผู้ใช้ "' . $user->name . '" ถูกอัปเดตเรียบร้อยแล้ว');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->getDatabaseErrorMessage($e)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * ลบข้อมูลผู้ใช้
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        DB::beginTransaction();
        
        try {
            $userName = $user->name;
            
            // Don't allow deleting your own account
            if (auth()->id() === $user->id) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
            }
            
            // Don't allow deleting the last admin
            if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'ไม่สามารถลบผู้ดูแลระบบคนสุดท้ายได้');
            }
            
            // Delete profile image if exists
            if ($user->profile_image) {
                if (file_exists(storage_path('app/public/' . $user->profile_image))) {
                    unlink(storage_path('app/public/' . $user->profile_image));
                }
            }
            
            $user->delete();
            
            DB::commit();
            
            Log::info('User deleted', ['id' => $user->id, 'name' => $userName]);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'ผู้ใช้ "' . $userName . '" ถูกลบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'id' => $user->id, 
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'error' => 'ไม่สามารถลบผู้ใช้ได้: ' . $e->getMessage()
            ]);
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
