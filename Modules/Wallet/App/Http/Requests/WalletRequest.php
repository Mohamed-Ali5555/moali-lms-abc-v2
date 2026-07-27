<?php

namespace Modules\Wallet\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'nullable',
            'student_id' => 'required|array',
            'student_id.*' => 'integer', 
            'balance' => 'required|numeric',
            'note' => 'nullable',
        ];
    }


    public function messages()
    {
        return [
            // 'type.required' => 'الرجاء اختيار نوع الوسيله ',
            'balance.required' => 'الرجاء إدخال القيمه ',
            'student_id.required' => 'الرجاء اختيار الطالب  ',

        ];
    }
}
