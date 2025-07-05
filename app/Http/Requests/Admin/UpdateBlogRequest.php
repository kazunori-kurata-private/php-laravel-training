<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'max:255'],
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2000',
                'dimensions:min_width=300,min_height=300,max_width=1200,max_height=1200',
            ],
            'body' => ['required', 'max:20000'],
            'cats.*' => ['distinct', 'exists:cats,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'カテゴリ',
            'title' => 'タイトル',
            'image' => '画像ファイル',
            'body' => '本文',
            'cats.*' => '登場するねこ',
        ];
    }
}
