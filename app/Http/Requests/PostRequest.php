<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Post::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('post'));
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:4', 'max:120'],
            'content' => ['required', 'string', 'min:6', 'max:10000'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'cover_image' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:4096'],
            'published_at' => ['nullable', 'date', 'after_or_equal:now', 'required_if:status,scheduled'],
            'images' => ['array', 'min:1', 'max:10'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,png,jpeg,webp', 'max:4096'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags' => ['required', 'array', 'min:1', 'max:5'],
            'tags.*' => ['distinct', 'exists:tags,id']
        ];
    }
}
