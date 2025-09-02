<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('product_category') ? $this->route('product_category')->category_id : null;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('product_categories', 'name')->ignore($categoryId, 'category_id'),
            ],
            'description' => 'nullable|string',
            'parent_category_id' => [
                'nullable', 'integer', 'exists:product_categories,category_id',
                Rule::notIn([$categoryId]), // Cannot be its own parent
            ],
        ];
    }
}