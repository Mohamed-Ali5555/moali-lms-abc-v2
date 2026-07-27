<?php

namespace Modules\BookStore\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankQuestionsRequest extends FormRequest
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
            'title'         => 'required',
            'price'         => 'required',
            'category_id'   => 'required',
        ];
    }


    public function messages()
    {
        return [
            'category_id.required' => 'الرجاء اختيار الصف الدراسي ',
            'price.required' => 'الرجاء إدخال القيمه ',
            'title.required' => ' الرجاء ادخال العنوان ',
        ];
    }
}
