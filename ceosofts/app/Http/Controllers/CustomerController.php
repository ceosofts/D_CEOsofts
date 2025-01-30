<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Support\Facades\Log; // ✅ Import Log แค่ครั้งเดียว

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
            $query->where('code', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
        }

        $customers = $query->paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            if (Customer::where('email', $request->email)->exists()) {
                return redirect()->back()->withErrors(['error' => 'This email is already registered.']);
            }

            // สร้างรหัสลูกค้าอัตโนมัติ
            $lastCode = Customer::max('code'); 
            $lastNumber = $lastCode ? intval(substr($lastCode, 1)) : 0;
            $newCode = 'C' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // บันทึกข้อมูล
            $customer = Customer::create(array_merge($request->validated(), ['code' => $newCode]));

            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create customer: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     $customer = Customer::findOrFail($id);
    //     return view('customers.edit', compact('customer'));
    // }

    public function edit(string $id)
{
    $customer = Customer::findOrFail($id);

    // ✅ Log ตรวจสอบค่าที่ส่งไปยัง View
    \Log::info('Editing customer:', ['customer' => $customer]);

    return view('customers.edit', compact('customer'));
}


    /**
     * Update the specified resource in storage.
     */

    // public function update(UpdateCustomerRequest $request, string $id)
    // {
    //     $customer = Customer::findOrFail($id);
        
    //     Log::info('Updating customer: ' . $id, ['data' => $request->all()]);

    //     if (!$customer->update($request->validated())) {
    //         Log::error('Update failed for customer: ' . $id);
    //         return redirect()->back()->withErrors(['error' => 'Update failed']);
    //     }

    //     Log::info('Customer updated successfully: ' . $id);
        
    //     return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    // }

    

public function update(UpdateCustomerRequest $request, string $id)
{
    $customer = Customer::findOrFail($id);

    // ✅ Log เช็คค่าที่รับมา
    Log::info('Updating customer: ' . $id, ['data' => $request->all()]);

    // ✅ ตรวจสอบว่าค่าที่รับมาตรงกับฐานข้อมูลหรือไม่
    Log::info('Current customer data', ['customer' => $customer->toArray()]);

    // ✅ ตรวจสอบว่ามีลูกค้ารายอื่นใช้ email นี้อยู่หรือไม่
    $exists = Customer::where('email', $request->email)
                      ->where('id', '!=', $id)
                      ->exists();

    if ($exists) {
        Log::error('Email already in use by another customer.');
        return redirect()->back()->withErrors(['error' => 'This email is already registered by another customer.']);
    }

    // ✅ อัปเดตข้อมูล
    $customer->update($request->validated());

    Log::info('Customer updated successfully: ' . $id);
    
    return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
