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
        $users = User::with('department')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.users.create', compact('departments'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|min:8',
    //         'role' => 'required|in:admin,manager,leader,user',
    //         'department_id' => 'nullable|exists:departments,id'
    //     ]);

    //     User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'role' => $request->role,
    //         'department_id' => $request->department_id
    //     ]);

    //     return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    // }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'role' => 'required|in:admin,manager,leader,user',
        'department_id' => 'nullable|exists:departments,id'
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'department_id' => $request->department_id
    ]);

    return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
}



    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    
    // public function update(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'role' => 'required|in:admin,manager,leader,user',
    //         'department_id' => 'nullable|exists:departments,id',
    //         'password' => 'nullable|min:8' // ✅ แก้ไขให้ password ไม่จำเป็นต้องใส่
    //     ]);

    //     // ✅ ตรวจสอบว่ามีการส่ง password ใหม่มาหรือไม่
    //     $data = [
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'role' => $request->role,
    //         'department_id' => $request->department_id
    //     ];

    //     if ($request->filled('password')) {
    //         $data['password'] = Hash::make($request->password);
    //     }

    //     $user->update($data);

    //     return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    // }

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
        'role' => $request->role,
        'department_id' => $request->department_id
    ];

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
}


    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete user: ' . $e->getMessage());
        }
    }
}
