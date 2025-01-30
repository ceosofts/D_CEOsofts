<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // ✅ Import Log

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // อนุญาตให้ดำเนินการ
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   
//      public function rules(): array
// {
//     $customerId = $this->route('customer'); // ดึงค่า ID ของลูกค้า

//     Log::info('Customer ID in UpdateCustomerRequest:', ['customerId' => $customerId]);

//     return [
//         'name' => 'required|string|max:255',

//         // ✅ แก้ปัญหา email ซ้ำ แต่ยกเว้นตัวเอง
//         'email' => [
//             'required',
//             'email',
//             Rule::unique('customers', 'email')->ignore($customerId),
//         ],

//         'phone' => 'nullable|string|max:15',
//         'address' => 'nullable|string|max:500',
//         'taxid' => 'nullable|string|max:20',

//         // ✅ แก้ปัญหา code ซ้ำ แต่ยกเว้นตัวเอง
//         'code' => [
//             'required',
//             'string',
//             'max:10',
//             Rule::unique('customers', 'code')->ignore($customerId),
//         ],
//     ];
// }



public function rules(): array
{
    $customerId = $this->route('customer'); // ดึงค่า ID ของลูกค้าที่กำลังอัปเดต

    // ✅ Log เพื่อตรวจสอบค่า customerId
    Log::info('Customer ID in UpdateCustomerRequest:', ['customerId' => $customerId]);

    return [
        'name' => 'required|string|max:255',

        // ✅ ตรวจสอบว่า email ซ้ำหรือไม่ แต่ยกเว้นตัวเอง
        'email' => [
            'required',
            'email',
            Rule::unique('customers', 'email')->ignore($customerId),
        ],

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
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'This email is already in use.',
            'code.unique' => 'This code is already taken.',
        ];
    }
}
