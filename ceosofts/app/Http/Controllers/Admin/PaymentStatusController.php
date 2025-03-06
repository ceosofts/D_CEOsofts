<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentStatusController extends Controller
{
    /**
     * แสดงรายการสถานะการจ่ายเงินทั้งหมด
     */
    public function index()
    {
        // สามารถใช้ paginate ถ้าต้องการแบ่งหน้า
        $statuses = PaymentStatus::paginate(10);
        return \view('admin.payment_statuses.index', compact('statuses'));
    }

    /**
     * แสดงฟอร์มสร้างสถานะการจ่ายเงินใหม่
     */
    public function create()
    {
        return \view('admin.payment_statuses.create');
    }

    /**
     * บันทึกข้อมูลสถานะการจ่ายเงินใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:payment_statuses,name|max:255'
        ]);

        try {
            $paymentStatus = new PaymentStatus();
            $paymentStatus->fill($validated);
            $paymentStatus->save();

            return \redirect()->route('admin.payment_statuses.index')
                ->with('success', 'เพิ่มข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error storing payment status: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลสถานะการจ่ายเงิน
     */
    public function edit(PaymentStatus $paymentStatus)
    {
        return \view('admin.payment_statuses.edit', compact('paymentStatus'));
    }

    /**
     * อัปเดตข้อมูลสถานะการจ่ายเงิน
     */
    public function update(Request $request, PaymentStatus $paymentStatus)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:payment_statuses,name,' . $paymentStatus->id,
        ]);

        try {
            $paymentStatus->forceFill($validated);
            $paymentStatus->save();

            return \redirect()->route('admin.payment_statuses.index')
                ->with('success', 'แก้ไขข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error updating payment status: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลสถานะการจ่ายเงิน
     */
    public function destroy(PaymentStatus $paymentStatus)
    {
        try {
            $paymentStatus->delete();
            return \redirect()->route('admin.payment_statuses.index')
                ->with('success', 'ลบข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error deleting payment status: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
