<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Traits\ValidatesContactable;

class UpdateContactRequest extends FormRequest
{
    use ValidatesContactable;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure the user is logged in to perform this action.
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $contactRules = [
            
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('contacts')->ignore($this->contact->contact_id, 'contact_id')],
            'phone' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ];

        return array_merge($contactRules, $this->contactableRules());
    }
}