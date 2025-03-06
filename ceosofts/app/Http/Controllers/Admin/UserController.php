<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * แสดงรายการผู้ใช้พร้อม Role และ Department
     */
    public function index()
    {
        // ใช้ paginate เพื่อแบ่งหน้า
        $users = User::paginate(10);
        // โหลด relationship roles และ department
        $users->load(['roles', 'department']);

        return \view('admin.users.index', compact('users'));
    }

    /**
     * แสดงฟอร์มสร้างผู้ใช้ใหม่
     */
    public function create()
    {
        // ดึง Role ทั้งหมดจากฐานข้อมูล (ใช้ Spatie Role)
        $roles = Role::all();
        // ดึงข้อมูลแผนกทั้งหมด
        $departments = Department::all();

        return \view('admin.users.create', compact('roles', 'departments'));
    }

    /**
     * บันทึกผู้ใช้ใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8',
            'role'          => 'required|in:admin,manager,leader,user',
            'department_id' => 'required|exists:departments,id',
        ]);

        try {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->department_id = $validated['department_id'];
            $user->save();

            // Assign role หลังจากสร้างผู้ใช้
            $user->assignRole($validated['role']);

            return \redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('Error storing user: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลผู้ใช้
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        return \view('admin.users.edit', compact('user', 'departments'));
    }

    /**
     * อัปเดตข้อมูลผู้ใช้ในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role'          => 'required|in:admin,manager,leader,user',
            'department_id' => 'nullable|exists:departments,id',
            'password'      => 'nullable|min:8'
        ]);

        try {
            $data = [
                'name'          => $validated['name'],
                'email'         => $validated['email'],
                'department_id' => $validated['department_id'] ?? $user->department_id,
            ];

            // ถ้ามีการกรอก password ให้ทำการ Hash ก่อนเก็บ
            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);
            // เปลี่ยน Role ของผู้ใช้ด้วยการ sync
            $user->syncRoles([$validated['role']]);

            return \redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลผู้ใช้
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // ไม่อนุญาตให้ลบ Admin
        if ($user->hasRole('admin')) {
            return \redirect()->route('admin.users.index')->with('error', 'ไม่สามารถลบ Admin ได้');
        }

        try {
            $user->delete();
            return \redirect()->route('admin.users.index')
                ->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
