<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductFeatureRequest extends FormRequest
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
        $featureId = $this->route('product_feature') ? $this->route('product_feature')->feature_id : null;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('product_features', 'name')->ignore($featureId, 'feature_id'),
            ],
            'description' => 'nullable|string',
        ];
    }
}