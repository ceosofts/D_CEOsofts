<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // อาจจะต้องปรับตามสิทธิ์การใช้งาน
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_name' => 'required|max:255',
            'branch' => 'sometimes|nullable|integer',
            'branch_description' => 'sometimes|nullable|string|max:500',
            'address' => 'sometimes|nullable|string|max:1000',
            'phone' => 'sometimes|nullable|string|max:50',
            'mobile' => 'sometimes|nullable|string|max:50',
            'fax' => 'sometimes|nullable|string|max:50',
            'email' => 'sometimes|nullable|email|max:255',
            'website' => 'sometimes|nullable|url|max:255',
            'logo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tax_id' => 'sometimes|nullable|string|max:50',
            'contact_person' => 'sometimes|nullable|string|max:255',
            'facebook' => 'sometimes|nullable|string|max:255',
            'twitter' => 'sometimes|nullable|string|max:255',
            'instagram' => 'sometimes|nullable|string|max:255',
            'linkedin' => 'sometimes|nullable|string|max:255',
            'youtube' => 'sometimes|nullable|string|max:255',
            'tiktok' => 'sometimes|nullable|string|max:255',
            'line' => 'sometimes|nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company_name.required' => 'กรุณาระบุชื่อบริษัท',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'website.url' => 'รูปแบบเว็บไซต์ไม่ถูกต้อง',
            'logo.image' => 'โลโก้ต้องเป็นไฟล์รูปภาพเท่านั้น',
            'logo.mimes' => 'โลโก้ต้องเป็นไฟล์ประเภท jpeg, png, jpg หรือ gif',
            'logo.max' => 'โลโก้ต้องมีขนาดไม่เกิน 2MB',
        ];
    }
}
