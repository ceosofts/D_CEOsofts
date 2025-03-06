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
     * ดึงข้อมูลลูกค้าจากฐานข้อมูล และส่งไปยัง view customers.index
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        $customers = $query->paginate(10);
        return \view('customers.index', compact('customers'));
    }

    /**
     * แสดงฟอร์มสร้างลูกค้าใหม่
     */
    public function create()
    {
        return \view('customers.create');
    }

    /**
     * บันทึกลูกค้าใหม่ลงในฐานข้อมูล
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            // ตรวจสอบว่า email นี้มีอยู่แล้วหรือไม่
            if (Customer::where('email', $request->email)->exists()) {
                return \redirect()->back()->withErrors(['error' => 'This email is already registered.']);
            }

            // สร้างรหัสลูกค้าอัตโนมัติ เช่น C0001, C0002, ...
            $lastCode = Customer::max('code');
            $lastNumber = $lastCode ? intval(substr($lastCode, 1)) : 0;
            $newCode = 'C' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // รวมข้อมูลที่ validate แล้ว พร้อมกับรหัสลูกค้าใหม่
            $data = $request->validated();
            $data['code'] = $newCode;

            $customer = Customer::create($data);

            return \redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create customer: ' . $e->getMessage());
            return \redirect()->back()->withErrors(['error' => 'Failed to create customer: ' . $e->getMessage()]);
        }
    }

    /**
     * แสดงรายละเอียดลูกค้า (View details)
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return \view('customers.show', compact('customer'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลลูกค้า
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        Log::info('Editing customer:', ['customer' => $customer->toArray()]);
        return \view('customers.edit', compact('customer'));
    }

    /**
     * อัปเดตข้อมูลลูกค้าในฐานข้อมูล
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        Log::info('Updating customer: ' . $id, ['data' => $request->all()]);
        Log::info('Current customer data:', ['customer' => $customer->toArray()]);

        // ตรวจสอบว่ามีลูกค้ารายอื่นใช้ email นี้อยู่หรือไม่
        $exists = Customer::where('email', $request->email)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            Log::error('Email already in use by another customer.');
            return \redirect()->back()->withErrors(['error' => 'This email is already registered by another customer.']);
        }

        $customer->update($request->validated());

        Log::info('Customer updated successfully: ' . $id);

        return \redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * ลบลูกค้าออกจากฐานข้อมูล
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return \redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
