<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentStatusController extends Controller
{
    /**
     * แสดงรายการสถานะการจ่ายเงินทั้งหมด
     */
    public function index()
    {
        $payment_statuses = PaymentStatus::all();
        return view('admin.payment_statuses.index', compact('payment_statuses'));
    }

    /**
     * แสดงฟอร์มสร้างสถานะการจ่ายเงินใหม่
     */
    public function create()
    {
        return view('admin.payment_statuses.create');
    }

    /**
     * บันทึกข้อมูลสถานะการจ่ายเงินใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle color field from color picker
        if ($request->has('color_text')) {
            $validated['color'] = $request->color_text;
        }

        // Default is_active to true if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        PaymentStatus::create($validated);

        return redirect('/admin/payment_statuses')
            ->with('flash_message', 'สร้างสถานะการจ่ายเงินใหม่สำเร็จแล้ว!');
    }

    /**
     * แสดงข้อมูลสถานะการจ่ายเงิน
     */
    public function show(string $id)
    {
        $payment_status = PaymentStatus::findOrFail($id);
        return view('admin.payment_statuses.show', compact('payment_status'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลสถานะการจ่ายเงิน
     * 
     * @param int $payment_status รหัสสถานะการจ่ายเงิน
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $payment_status = PaymentStatus::findOrFail($id);
        return view('admin.payment_statuses.edit', compact('payment_status'));
    }

    /**
     * อัปเดตข้อมูลสถานะการจ่ายเงิน
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle color field from color picker
        if ($request->has('color_text')) {
            $validated['color'] = $request->color_text;
        }

        // Default is_active to false if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        $payment_status = PaymentStatus::findOrFail($id);
        $payment_status->update($validated);

        return redirect('/admin/payment_statuses')
            ->with('flash_message', 'อัปเดตสถานะการจ่ายเงินสำเร็จแล้ว!');
    }

    /**
     * ลบข้อมูลสถานะการจ่ายเงิน
     */
    public function destroy(string $id)
    {
        $payment_status = PaymentStatus::findOrFail($id);
        $payment_status->delete();

        return redirect('/admin/payment_statuses')
            ->with('flash_message', 'ลบสถานะการจ่ายเงินสำเร็จแล้ว!');
    }
}
