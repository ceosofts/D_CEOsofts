<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\ItemStatus;
use App\Models\ItemStatus; // ✅ ต้อง import ตรงนี้

class ItemStatusController extends Controller
{
    public function index()
    {
        $statuses = ItemStatus::all();
        return view('admin.item_statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('admin.item_statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:item_statuses']);
        ItemStatus::create(['name' => $request->name]);
        return redirect()->route('admin.item_statuses.index')->with('success', 'เพิ่มสถานะสำเร็จ');
    }

    public function edit(ItemStatus $itemStatus)
    {
        return view('admin.item_statuses.edit', compact('itemStatus'));
    }

    public function update(Request $request, ItemStatus $itemStatus)
    {
        $request->validate(['name' => 'required|unique:item_statuses,name,' . $itemStatus->id]);
        $itemStatus->update(['name' => $request->name]);
        return redirect()->route('admin.item_statuses.index')->with('success', 'อัปเดตสถานะสำเร็จ');
    }

    public function destroy(ItemStatus $itemStatus)
    {
        $itemStatus->delete();
        return redirect()->route('admin.item_statuses.index')->with('success', 'ลบสถานะสำเร็จ');
    }
}
