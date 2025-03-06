<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
    /**
     * แสดงรายการตำแหน่งทั้งหมด
     */
    public function index()
    {
        // ใช้ paginate เพื่อแบ่งหน้า (สามารถเปลี่ยนเป็น all() ได้ถ้าต้องการแสดงทั้งหมด)
        $positions = Position::paginate(10);
        return \view('admin.positions.index', compact('positions'));
    }

    /**
     * แสดงฟอร์มสร้างตำแหน่งใหม่
     */
    public function create()
    {
        return \view('admin.positions.create');
    }

    /**
     * บันทึกตำแหน่งใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
        ]);

        try {
            $position = new Position();
            $position->fill($validated);
            $position->save();

            return \redirect()->route('admin.positions.index')
                ->with('success', 'เพิ่มตำแหน่งเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error storing position: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลตำแหน่ง
     */
    public function edit($id)
    {
        $position = Position::findOrFail($id);
        return \view('admin.positions.edit', compact('position'));
    }

    /**
     * อัปเดตข้อมูลตำแหน่งในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $id,
        ]);

        try {
            $position = Position::findOrFail($id);
            // ใช้ forceFill เพื่ออัปเดตข้อมูลแบบบังคับ
            $position->forceFill($validated);
            $position->save();

            return \redirect()->route('admin.positions.index')
                ->with('success', 'แก้ไขตำแหน่งเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error updating position: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลตำแหน่ง
     */
    public function destroy($id)
    {
        try {
            $position = Position::findOrFail($id);
            $position->delete();

            return \redirect()->route('admin.positions.index')
                ->with('success', 'ลบตำแหน่งเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error deleting position: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
