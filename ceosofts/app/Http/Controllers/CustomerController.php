<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer; // ใช้ Model Customer สำหรับการจัดการข้อมูลในฐานข้อมูล
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     * ฟังก์ชันนี้ดึงข้อมูลลูกค้าทั้งหมดจากฐานข้อมูล และส่งไปยัง View `customers.index`
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%');
        }

        $customers = $query->paginate(10); // ดึงข้อมูลลูกค้าพร้อมแบ่งหน้า
        return view('customers.index', compact('customers')); // ส่งข้อมูลไปยัง View
    }

    /**
     * Show the form for creating a new resource.
     * ฟังก์ชันนี้แสดงฟอร์มสำหรับเพิ่มลูกค้าใหม่
     */
    public function create()
    {
        return view('customers.create'); // แสดงหน้า View สำหรับสร้างลูกค้าใหม่
    }

    /** ยังไม่พร้อมใช้ตอนนี้
     * Store a newly created resource in storage.
     * ฟังก์ชันนี้บันทึกข้อมูลลูกค้าใหม่ลงในฐานข้อมูลหลังจากการตรวจสอบข้อมูล (Validation)
     */
    // public function store(StoreCustomerRequest $request)
    // {
    //     // บันทึกข้อมูลที่ผ่านการตรวจสอบลงฐานข้อมูล
    //     Customer::create($request->validated());

    //     // Redirect ไปยังหน้ารายการลูกค้าพร้อมข้อความสำเร็จ
    //     return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    // }

    public function store(StoreCustomerRequest $request)
    {
        try {
            // บันทึกข้อมูลที่ผ่านการตรวจสอบลงในฐานข้อมูล
            Customer::create($request->validated());

            // Redirect ไปยังหน้ารายการลูกค้าพร้อมข้อความสำเร็จ
            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            // จัดการข้อผิดพลาดและแสดงข้อความที่เหมาะสม
            return redirect()->back()->withErrors(['error' => 'Failed to create customer: ' . $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     * ฟังก์ชันนี้แสดงข้อมูลรายละเอียดของลูกค้าตาม ID ที่ระบุ
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id); // ดึงข้อมูลลูกค้าด้วย ID
        return view('customers.show', compact('customer')); // ส่งข้อมูลไปยัง View
    }

    /**
     * Show the form for editing the specified resource.
     * ฟังก์ชันนี้แสดงฟอร์มสำหรับแก้ไขข้อมูลลูกค้าตาม ID ที่ระบุ
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id); // ดึงข้อมูลลูกค้าด้วย ID
        return view('customers.edit', compact('customer')); // ส่งข้อมูลไปยัง View
    }

    /**
     * Update the specified resource in storage.
     * ฟังก์ชันนี้อัปเดตข้อมูลลูกค้าในฐานข้อมูลหลังจากการตรวจสอบข้อมูล (Validation)
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $customer = Customer::findOrFail($id); // ดึงข้อมูลลูกค้าด้วย ID
        $customer->update($request->validated()); // อัปเดตข้อมูลที่ผ่านการตรวจสอบ

        // Redirect ไปยังหน้ารายการลูกค้าพร้อมข้อความสำเร็จ
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    // public function update(UpdateCustomerRequest $request, string $id)
    // {
    //     $customer = Customer::findOrFail($id); // ดึงข้อมูลลูกค้า
    //     $customer->update($request->validated()); // ใช้ข้อมูลที่ผ่านการตรวจสอบแล้วอัปเดต

    //     return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    // }
    /**
     * Remove the specified resource from storage.
     * ฟังก์ชันนี้ลบข้อมูลลูกค้าออกจากฐานข้อมูลตาม ID ที่ระบุ
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id); // ดึงข้อมูลลูกค้าด้วย ID
        $customer->delete(); // ลบข้อมูลลูกค้าออกจากฐานข้อมูล

        // Redirect ไปยังหน้ารายการลูกค้าพร้อมข้อความสำเร็จ
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
