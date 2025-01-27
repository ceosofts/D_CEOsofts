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
        return true; // อนุญาตให้ใช้ Request นี้ (เปลี่ยนเป็น false หากต้องการตรวจสอบสิทธิ์ในอนาคต)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'taxid' => 'nullable|string|max:20', // เพิ่มการตรวจสอบสำหรับ taxid



        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'This email is already in use.',
            'taxid.max' => 'The tax ID must not exceed 20 characters.', // เพิ่มข้อความแจ้งข้อผิดพลาดสำหรับ taxid
        ];
    }
}
