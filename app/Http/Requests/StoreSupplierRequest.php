<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'legal_id' => 'required|string|max:100|unique:suppliers,legal_id',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'phone_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'addresses' => 'nullable|array|max:1', // For simplicity, one address block for now
            'addresses.*.address_type' => 'nullable|string|max:50',
            'addresses.*.street_address_line_1' => 'required_with:addresses.*.city,addresses.*.postal_code|string|max:255',
            'addresses.*.street_address_line_2' => 'nullable|string|max:255',
            'addresses.*.city' => 'required_with:addresses.*.street_address_line_1,addresses.*.postal_code|string|max:100',
            'addresses.*.state_province' => 'nullable|string|max:100',
            'addresses.*.postal_code' => 'required_with:addresses.*.street_address_line_1,addresses.*.city|string|max:20',
            'addresses.*.country_code' => 'nullable|string|size:2',
            'addresses.*.is_primary' => 'nullable|boolean',
        ];
    }
}