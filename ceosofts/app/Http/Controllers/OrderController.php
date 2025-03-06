<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * แสดงรายการคำสั่งซื้อทั้งหมด
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // หากมีโมเดล Order จริงๆ สามารถดึงข้อมูลได้เช่นนี้:
        // $orders = \App\Models\Order::paginate(10);
        // return \view('orders.index', compact('orders'));

        // สำหรับตอนนี้ ให้แสดง view แบบเปล่าไว้ก่อน
        return \view('orders.index');
    }

    /**
     * แสดงฟอร์มสำหรับสร้างคำสั่งซื้อใหม่
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return \view('orders.create');
    }

    /**
     * บันทึกคำสั่งซื้อใหม่ลงในฐานข้อมูล
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูล (ปรับเปลี่ยน validation rule ตามความต้องการ)
        $request->validate([
            // 'field' => 'required|string|max:255',
        ]);

        // หากมีโมเดล Order ให้ใช้:
        // \App\Models\Order::create($request->all());

        return \redirect()->route('orders.index')
            ->with('success', 'คำสั่งซื้อถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดของคำสั่งซื้อที่ระบุ
     *
     * @param  string  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(string $id)
    {
        // หากมีโมเดล Order สามารถดึงข้อมูลได้เช่นนี้:
        // $order = \App\Models\Order::findOrFail($id);
        // return \view('orders.show', compact('order'));

        return \view('orders.show');
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขคำสั่งซื้อที่ระบุ
     *
     * @param  string  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        // หากมีโมเดล Order สามารถดึงข้อมูลได้เช่นนี้:
        // $order = \App\Models\Order::findOrFail($id);
        // return \view('orders.edit', compact('order'));

        return \view('orders.edit');
    }

    /**
     * อัปเดตข้อมูลคำสั่งซื้อที่ระบุในฐานข้อมูล
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        // ตรวจสอบข้อมูล (ปรับแก้ validation rule ตามที่ต้องการ)
        $request->validate([
            // 'field' => 'required|string|max:255',
        ]);

        // หากมีโมเดล Order:
        // $order = \App\Models\Order::findOrFail($id);
        // $order->update($request->all());

        return \redirect()->route('orders.index')
            ->with('success', 'คำสั่งซื้อถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * ลบคำสั่งซื้อที่ระบุออกจากฐานข้อมูล
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        // หากมีโมเดล Order:
        // $order = \App\Models\Order::findOrFail($id);
        // $order->delete();

        return \redirect()->route('orders.index')
            ->with('success', 'คำสั่งซื้อถูกลบเรียบร้อยแล้ว');
    }
}
