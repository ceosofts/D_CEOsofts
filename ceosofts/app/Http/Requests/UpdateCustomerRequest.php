<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // อนุญาตให้ดำเนินการ
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // ดึงค่า ID ของลูกค้าที่กำลังอัปเดตจาก route
        // โดยสมมุติว่า route('customers.update') มีพารามิเตอร์เป็น {customer}
        // เช่น Route::put('customers/{customer}', [...])->name('customers.update');
        $customerId = $this->route('customer');

        // Log เพื่อตรวจสอบค่า customerId
        Log::info('Customer ID in UpdateCustomerRequest:', ['customerId' => $customerId]);

        return [
            // เปลี่ยนจาก name => companyname
            'companyname' => [
                'required',
                'string',
                'max:255',
            ],
            // เพิ่ม contact_name เป็น required
            'contact_name' => [
                'required',
                'string',
                'max:255',
            ],
            // ตรวจสอบว่า email ซ้ำหรือไม่ แต่ยกเว้นตัวเอง
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            // phone, address, taxid อาจจะไม่บังคับ
            'phone' => [
                'nullable',
                'string',
                'max:15',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'taxid' => [
                'nullable',
                'string',
                'max:20',
            ],
            // code บังคับไม่ซ้ำ
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('customers', 'code')->ignore($customerId),
            ],
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'companyname.required' => 'The company name field is required.',
            'contact_name.required' => 'The contact name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'This email is already in use.',
            'code.required' => 'The customer code field is required.',
            'code.unique' => 'This code is already taken.',
        ];
    }
}
