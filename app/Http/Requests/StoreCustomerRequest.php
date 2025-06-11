<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone_number' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_state' => 'nullable|string|max:100',
            'address_postal_code' => 'nullable|string|max:20',
            'address_country' => 'nullable|string|max:100',
             'status' => 'nullable|string|max:50|in:Active,Inactive,Lead,Prospect',
            'notes' => 'nullable|string',
            // 'created_by_user_id' will be set automatically
        ];
    }
}