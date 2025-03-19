<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::with('department')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        
        $departments = Department::all();
        
        return view('admin.users.index', compact('users', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = Department::all();
        
        // Get available roles or create default ones if using Spatie permissions
        try {
            $roles = Role::all();
        } catch (\Exception $e) {
            // If Role model doesn't exist or there's another issue, create an array with default roles
            $roles = collect([
                (object)['id' => 'admin', 'name' => 'admin'],
                (object)['id' => 'user', 'name' => 'user'],
                (object)['id' => 'manager', 'name' => 'manager'],
            ]);
        }
        
        return view('admin.users.create', compact('departments', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|string|in:admin,user,manager',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
        ]);
        
        // Assign role
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
                        ->with('success', 'เพิ่มผู้ใช้งานสำเร็จแล้ว');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::with('department')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id', // แก้ไขจาก department_id เป็น id
        ]);

        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->department_id = $request->department_id;
            $user->save();
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'อัปเดตผู้ใช้งานสำเร็จแล้ว');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in UserController@update: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting own account
        if (auth()->id() === $user->id) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }
        
        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'ลบผู้ใช้งานสำเร็จแล้ว');
    }
}
