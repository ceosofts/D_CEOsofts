<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // ✅ เพิ่มบรรทัดนี้
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
// class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments|max:255',
        ]);

        Department::create(['name' => $request->name]);

        // return redirect()->route('departments.index')->with('success', 'Department created successfully.');
        // return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully.');

        

    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|unique:departments,name,' . $department->id . '|max:255',
        ]);

        $department->update(['name' => $request->name]);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
