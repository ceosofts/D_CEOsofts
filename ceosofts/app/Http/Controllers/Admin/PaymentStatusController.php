<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    public function index()
    {
        $statuses = PaymentStatus::all();
        return view('admin.payment_statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('admin.payment_statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:payment_statuses|max:255'
        ]);

        PaymentStatus::create($request->all());

        return redirect()->route('admin.payment_statuses.index')->with('success', 'เพิ่มข้อมูลสำเร็จ');
    }

    public function edit(PaymentStatus $paymentStatus)
    {
        return view('admin.payment_statuses.edit', compact('paymentStatus'));
    }

    public function update(Request $request, PaymentStatus $paymentStatus)
    {
        $request->validate([
            'name' => 'required|max:255|unique:payment_statuses,name,' . $paymentStatus->id
        ]);

        $paymentStatus->update($request->all());

        return redirect()->route('admin.payment_statuses.index')->with('success', 'แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy(PaymentStatus $paymentStatus)
    {
        $paymentStatus->delete();
        return redirect()->route('admin.payment_statuses.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
