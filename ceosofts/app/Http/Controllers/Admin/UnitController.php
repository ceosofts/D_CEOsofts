<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:units,name|max:255',
        ]);

        Unit::create($request->all());

        return redirect()->route('admin.units.index')->with('success', 'หน่วยสินค้าเพิ่มสำเร็จ');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|unique:units,name,' . $unit->id . '|max:255',
        ]);

        $unit->update($request->all());

        return redirect()->route('units.index')->with('success', 'อัปเดตหน่วยสินค้าสำเร็จ');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.units.index')->with('success', 'ลบหน่วยสินค้าสำเร็จ');
    }

        public function show(Unit $unit)
    {
        return view('admin.units.show', compact('unit'));
    }

}
