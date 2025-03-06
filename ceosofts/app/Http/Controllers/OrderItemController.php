<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class OrderItemController extends Controller
{
    /**
     * แสดงรายการ order items สำหรับคำสั่งซื้อที่ระบุ
     *
     * @param  int  $orderId
     * @return \Illuminate\Contracts\View\View
     */
    public function index($orderId)
    {
        $orderItems = OrderItem::where('order_id', $orderId)->paginate(10);
        return \view('order_items.index', compact('orderItems', 'orderId'));
    }

    /**
     * แสดงฟอร์มสร้าง order item ใหม่สำหรับคำสั่งซื้อที่ระบุ
     *
     * @param  int  $orderId
     * @return \Illuminate\Contracts\View\View
     */
    public function create($orderId)
    {
        return \view('order_items.create', compact('orderId'));
    }

    /**
     * บันทึก order item ใหม่ลงในฐานข้อมูล
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $orderId)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            // เพิ่มฟิลด์อื่น ๆ ที่จำเป็นตามที่โปรเจคต้องการ
        ]);

        $validated['order_id'] = $orderId;

        OrderItem::create($validated);

        return \redirect()->route('orders.order-items.index', $orderId)
            ->with('success', 'Order item created successfully.');
    }

    /**
     * แสดงรายละเอียดของ order item ที่ระบุ
     *
     * @param  int  $orderId
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($orderId, $id)
    {
        $orderItem = OrderItem::where('order_id', $orderId)->findOrFail($id);
        return \view('order_items.show', compact('orderItem', 'orderId'));
    }

    /**
     * แสดงฟอร์มแก้ไข order item ที่ระบุ
     *
     * @param  int  $orderId
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($orderId, $id)
    {
        $orderItem = OrderItem::where('order_id', $orderId)->findOrFail($id);
        return \view('order_items.edit', compact('orderItem', 'orderId'));
    }

    /**
     * อัปเดต order item ที่ระบุในฐานข้อมูล
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $orderId, $id)
    {
        $orderItem = OrderItem::where('order_id', $orderId)->findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            // เพิ่มฟิลด์อื่น ๆ ที่จำเป็นสำหรับ order item
        ]);

        $orderItem->update($validated);

        return \redirect()->route('orders.order-items.index', $orderId)
            ->with('success', 'Order item updated successfully.');
    }

    /**
     * ลบ order item ที่ระบุออกจากฐานข้อมูล
     *
     * @param  int  $orderId
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($orderId, $id)
    {
        $orderItem = OrderItem::where('order_id', $orderId)->findOrFail($id);
        $orderItem->delete();

        return \redirect()->route('orders.order-items.index', $orderId)
            ->with('success', 'Order item deleted successfully.');
    }
}
