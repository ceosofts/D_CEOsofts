<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * แสดงรายการลูกค้าทั้งหมด
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // หากมีการค้นหา
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('companyname', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // แสดงผลแบบแบ่งหน้า
        $customers = $query->paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * แสดงฟอร์มสร้างลูกค้าใหม่
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * บันทึกลูกค้าใหม่ลงในฐานข้อมูล
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            // ตรวจสอบอีเมลซ้ำ
            if (Customer::where('email', $request->email)->exists()) {
                return redirect()->back()->withErrors(['error' => 'This email is already registered.']);
            }

            // สร้างรหัสลูกค้าอัตโนมัติในรูปแบบ "CLSxxxx"
            // ตัวอย่าง code เดิม: "CLS0001"
            $lastCode = Customer::max('code'); // ดึง code สูงสุด เช่น "CLS0001"
            $lastNumber = $lastCode ? intval(substr($lastCode, 3)) : 0;
            $newCode = 'CLS' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // รับข้อมูลที่ผ่านการ validate แล้วจาก StoreCustomerRequest
            // ควร validate ฟิลด์: companyname, contact_name, email, phone, address, taxid
            $data = $request->validated();
            $data['code'] = $newCode;

            // สร้างลูกค้า
            $customer = Customer::create($data);

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create customer: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to create customer: ' . $e->getMessage()]);
        }
    }

    /**
     * แสดงรายละเอียดลูกค้า
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลลูกค้า
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        Log::info('Editing customer:', ['customer' => $customer->toArray()]);
        return view('customers.edit', compact('customer'));
    }

    /**
     * อัปเดตข้อมูลลูกค้าในฐานข้อมูล
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        Log::info('Updating customer: ' . $id, ['data' => $request->all()]);
        Log::info('Current customer data:', ['customer' => $customer->toArray()]);

        // ตรวจสอบอีเมลซ้ำ
        $exists = Customer::where('email', $request->email)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            Log::error('Email already in use by another customer.');
            return redirect()->back()->withErrors(['error' => 'This email is already registered by another customer.']);
        }

        // อัปเดตข้อมูลลูกค้าโดยใช้ข้อมูลที่ validate แล้ว
        $customer->update($request->validated());

        Log::info('Customer updated successfully: ' . $id);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * ลบลูกค้าออกจากฐานข้อมูล
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
