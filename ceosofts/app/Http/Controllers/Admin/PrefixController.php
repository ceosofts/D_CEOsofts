<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prefix;
use Illuminate\Support\Facades\Log;

class PrefixController extends Controller
{
    /**
     * แสดงรายการคำนำหน้าชื่อทั้งหมด
     */
    public function index()
    {
        // ใช้ paginate เพื่อแบ่งหน้า (หากต้องการแสดงทั้งหมดให้ใช้ all())
        $prefixes = Prefix::paginate(10);
        return \view('admin.prefixes.index', compact('prefixes'));
    }

    /**
     * แสดงฟอร์มสร้างคำนำหน้าชื่อใหม่
     */
    public function create()
    {
        return \view('admin.prefixes.create');
    }

    /**
     * บันทึกข้อมูลคำนำหน้าชื่อใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:prefixes,name',
        ]);

        try {
            $prefix = new Prefix();
            $prefix->fill($validated);
            $prefix->save();

            return \redirect()->route('admin.prefixes.index')
                ->with('success', 'เพิ่มคำนำหน้าชื่อเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error storing prefix: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลคำนำหน้าชื่อ
     */
    public function edit(Prefix $prefix)
    {
        return \view('admin.prefixes.edit', compact('prefix'));
    }

    /**
     * อัปเดตข้อมูลคำนำหน้าชื่อในฐานข้อมูล
     */
    public function update(Request $request, Prefix $prefix)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:prefixes,name,' . $prefix->id,
        ]);

        try {
            $prefix->forceFill($validated);
            $prefix->save();

            return \redirect()->route('admin.prefixes.index')
                ->with('success', 'แก้ไขคำนำหน้าชื่อเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error updating prefix: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลคำนำหน้าชื่อ
     */
    public function destroy(Prefix $prefix)
    {
        try {
            $prefix->delete();
            return \redirect()->route('admin.prefixes.index')
                ->with('success', 'ลบคำนำหน้าชื่อเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error deleting prefix: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
