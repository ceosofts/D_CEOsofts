<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Authorization is handled via policies in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paymentStatusId = $this->route('payment_status.id') ?? $this->route('payment_status');
        
        if ($paymentStatusId && is_object($paymentStatusId)) {
            $paymentStatusId = $paymentStatusId->id;
        }
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_statuses')->ignore($paymentStatusId),
            ],
            'description' => 'nullable|string|max:1000',
            'color' => [
                'required',
                'string',
                'max:7',
                'regex:/^#[0-9a-fA-F]{6}$/',
            ],
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'กรุณาระบุชื่อสถานะการชำระเงิน',
            'name.max' => 'ชื่อสถานะการชำระเงินต้องไม่เกิน 255 ตัวอักษร',
            'name.unique' => 'ชื่อสถานะการชำระเงินนี้มีอยู่ในระบบแล้ว',
            'description.max' => 'คำอธิบายต้องไม่เกิน 1000 ตัวอักษร',
            'color.required' => 'กรุณาระบุสีของสถานะ',
            'color.regex' => 'สีต้องอยู่ในรูปแบบ Hex Color Code (เช่น #FF5733)',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Ensure color has # prefix if not provided
        if ($this->has('color')) {
            $color = $this->get('color');
            if (strlen($color) === 6 && !str_starts_with($color, '#')) {
                $this->merge([
                    'color' => '#' . $color
                ]);
            }
        }
    }
}
