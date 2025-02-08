<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::all();
        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
        ]);

        Position::create(['name' => $request->name]);

        return redirect()->route('admin.positions.index')->with('success', 'เพิ่มตำแหน่งเรียบร้อยแล้ว!');
    }

    public function edit($id)
    {
        $position = Position::findOrFail($id);
        return view('admin.positions.edit', compact('position'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $id,
        ]);

        $position = Position::findOrFail($id);
        $position->update(['name' => $request->name]);

        return redirect()->route('admin.positions.index')->with('success', 'แก้ไขตำแหน่งเรียบร้อยแล้ว!');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->route('admin.positions.index')->with('success', 'ลบตำแหน่งเรียบร้อยแล้ว!');
    }
}
