<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\ValidatesContactable;


class StoreContactRequest extends FormRequest
{
    use ValidatesContactable;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow if the user is authenticated.
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
            'email' => ['nullable', 'email', 'max:255', 'unique:contacts,email'],
            'phone' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ];

        return array_merge($contactRules, $this->contactableRules());
    }
}
