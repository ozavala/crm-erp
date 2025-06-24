<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer') ? $this->route('customer')->customer_id : null;

        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('customers', 'email')->ignore($customerId, 'customer_id'),
            ],
            'phone_number' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_state' => 'nullable|string|max:100',
            'address_postal_code' => 'nullable|string|max:20',
            'address_country' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50|in:Active,Inactive,Lead,Prospect',
            //'notes' => 'nullable|string',
             // New Address Fields (assuming one address block for now, indexed at 0)
            'addresses' => 'nullable|array|max:1', // Allow only one address block for now
            'addresses.*.address_id' => 'nullable|integer|exists:addresses,address_id', // For updates
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