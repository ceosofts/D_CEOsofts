<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxSetting;
use Illuminate\Http\Request;

class TaxSettingController extends Controller
{
    public function index()
    {
        $taxes = TaxSetting::all();
        return view('admin.tax.index', compact('taxes'));
    }

    public function create()
    {
        return view('admin.tax.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tax_settings,name',
            'rate' => 'required|numeric|min:0',
        ]);

        TaxSetting::create($request->all());

        return redirect()->route('admin.tax.index')->with('success', 'เพิ่มข้อมูลภาษีสำเร็จ');
    }

    public function edit(TaxSetting $tax)
    {
        return view('admin.tax.edit', compact('tax'));
    }

    public function update(Request $request, TaxSetting $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tax_settings,name,' . $tax->id,
            'rate' => 'required|numeric|min:0',
        ]);

        $tax->update($request->all());

        return redirect()->route('admin.tax.index')->with('success', 'อัปเดตข้อมูลภาษีสำเร็จ');
    }

    public function destroy(TaxSetting $tax)
    {
        $tax->delete();
        return redirect()->route('admin.tax.index')->with('success', 'ลบข้อมูลภาษีสำเร็จ');
    }

    public function show(TaxSetting $tax)
    {
        return view('admin.tax.show', compact('tax'));
    }
}
