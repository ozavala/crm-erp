<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAddressRequest extends FormRequest
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
        // Basic validation for address fields.
        // Validating addressable_id against addressable_type dynamically is complex here.
        // Typically, addresses are created in the context of their parent.
        return [
            'addressable_id' => 'required|integer',
            'addressable_type' => 'required|string|max:255', // e.g., App\Models\Customer
            'address_type' => 'nullable|string|max:255',
            'street_address_line_1' => 'required|string|max:255',
            'street_address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state_province' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country_code' => 'required|string|size:2',
            'is_primary' => 'nullable|boolean',
        ];
    }
}