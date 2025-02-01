<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // ✅ โหลด Users พร้อม Role และ Department
        $users = User::paginate(10);
        $users->load(['roles', 'department']); 
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,manager,leader,user',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
        ]);

        $user->assignRole($request->role); // ✅ Assign Role หลังจากสร้าง User

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,leader,user',
            'department_id' => 'nullable|exists:departments,id',
            'password' => 'nullable|min:8'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]); // ✅ เปลี่ยน Role ของ User

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')->with('error', 'ไม่สามารถลบ Admin ได้');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }
}
