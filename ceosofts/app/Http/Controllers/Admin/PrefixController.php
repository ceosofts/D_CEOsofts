<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prefix;

class PrefixController extends Controller
{
    public function index()
    {
        $prefixes = Prefix::all();
        return view('admin.prefixes.index', compact('prefixes'));
    }

    public function create()
    {
        return view('admin.prefixes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:prefixes,name',
        ]);

        Prefix::create(['name' => $request->name]);

        return redirect()->route('admin.prefixes.index')->with('success', 'เพิ่มคำนำหน้าชื่อเรียบร้อยแล้ว!');
    }

    public function edit(Prefix $prefix)
    {
        return view('admin.prefixes.edit', compact('prefix'));
    }

    public function update(Request $request, Prefix $prefix)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:prefixes,name,' . $prefix->id,
        ]);

        $prefix->update(['name' => $request->name]);

        return redirect()->route('admin.prefixes.index')->with('success', 'แก้ไขคำนำหน้าชื่อเรียบร้อยแล้ว!');
    }

    public function destroy(Prefix $prefix)
    {
        $prefix->delete();

        return redirect()->route('admin.prefixes.index')->with('success', 'ลบคำนำหน้าชื่อเรียบร้อยแล้ว!');
    }
}
