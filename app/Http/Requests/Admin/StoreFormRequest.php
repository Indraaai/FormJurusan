<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user() && auth()->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'is_published' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|max:500',
            'questions.*.type' => 'required|in:text,email,number,textarea,select,radio,checkbox,file',
            'questions.*.required' => 'boolean',
            'questions.*.options' => 'array',
            'questions.*.options.*' => 'string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul form wajib diisi',
            'title.min' => 'Judul form minimal 3 karakter',
            'questions.required' => 'Form harus memiliki minimal 1 pertanyaan',
            'questions.*.text.required' => 'Teks pertanyaan wajib diisi',
            'questions.*.type.in' => 'Tipe pertanyaan tidak valid',
        ];
    }
}
