<?php

namespace Modules\BookStore\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookStoreRequest extends FormRequest
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
        $isUpdate = !empty($this->route('id')) || !empty($this->id);

        return [
            'title'       => 'required',
            'price'       => 'required',
            'category_id' => 'required',
            'thumbnail'   => $isUpdate
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
                : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'file_type'   => ['required', Rule::in(['file', 'link'])],
            'book_file'   => [
                Rule::requiredIf(fn () => $this->file_type === 'file' && !$isUpdate),
                'nullable',
                'file',
                'mimes:pdf',
                'max:51200',
            ],
            'file_url'    => [
                Rule::requiredIf(fn () => $this->file_type === 'link'),
                'nullable',
                'url',
                'max:2000',
            ],
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'الرجاء اختيار الصف الدراسي ',
            'price.required'       => 'الرجاء إدخال القيمه ',
            'title.required'       => ' الرجاء ادخال العنوان ',
            'thumbnail.required'   => ' الرجاء اختيار الصوره  ',
            'file_type.required'   => 'الرجاء اختيار نوع الملف (تحميل أو رابط)',
            'book_file.required'   => 'الرجاء رفع ملف PDF',
            'book_file.mimes'      => 'الملف يجب أن يكون بصيغة PDF',
            'file_url.required'    => 'الرجاء إدخال رابط الكتاب',
            'file_url.url'         => 'الرجاء إدخال رابط صحيح',
        ];
    }
}
