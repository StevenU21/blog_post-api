<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:4', 'max:40'],
            'content' => ['required', 'string', 'min:6', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:4096'],
            'category_id' => ['required', 'exists:categories,id'],
            'user_id' => ['exists:users,id'],
            'labels' => ['required', 'array', 'min:1', 'max:5'],
            'labels.*' => ['required', 'string', 'distinct', 'exists:labels,id']
        ];
    }
}
