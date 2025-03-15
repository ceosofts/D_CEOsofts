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
            'companyname'   => 'required|string|max:255',
            'contact_name'  => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'address'       => 'required|string',
            'taxid'         => 'required|string|size:13|regex:/^[0-9]+$/', // เพิ่มการตรวจสอบรูปแบบ Tax ID
            'branch'        => 'nullable|string|max:255',
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
            'branch.max'           => 'The branch name must not exceed 255 characters.',
            'branch.string'        => 'The branch must be a text string.'
        ];
    }
}
