<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // อนุญาตให้ใช้ Request นี้ (เปลี่ยนเป็น false หากต้องการตรวจสอบสิทธิ์ในอนาคต)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // เปลี่ยนจาก name => companyname
            'companyname'   => 'required|string|max:255',
            // เพิ่ม contact_name เป็น required
            'contact_name'  => 'required|string|max:255',
            'email'         => 'required|email|unique:customers,email',
            'phone'         => 'nullable|string|max:15',
            'address'       => 'nullable|string|max:500',
            'taxid'         => 'nullable|string|max:20',
            // 'code'       => 'required|string|unique:customers,code', // ถ้าต้องการบังคับ unique code
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'companyname.required' => 'The company name field is required.',
            'contact_name.required' => 'The contact name field is required.',
            'email.required'       => 'The email field is required.',
            'email.unique'         => 'This email is already in use.',
            'taxid.max'            => 'The tax ID must not exceed 20 characters.',
        ];
    }
}
