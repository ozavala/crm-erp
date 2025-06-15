<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed, e.g., check if user can access the lead
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
            'type' => 'required|string|max:50', // e.g., Call, Email, Meeting
            'description' => 'required|string',
            'activity_date' => 'required|date',
            // 'lead_id' will be taken from the route parameter, not the form input directly
            // 'user_id' will be the authenticated user
        ];
    }
}