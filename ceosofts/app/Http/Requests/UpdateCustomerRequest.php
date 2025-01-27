<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ตั้งค่าเป็น true เพื่ออนุญาตให้ดำเนินการ
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
            'email' => 'required|email|unique:customers,email,' . $this->route('customer'), // ตรวจสอบ unique แต่ยกเว้นอีเมลของลูกค้าปัจจุบัน
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'taxid' => 'nullable|string|max:20', // เพิ่มกฎสำหรับ taxid
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
        ];
    }
}
