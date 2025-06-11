<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed (e.g., based on user permissions)
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
            'name' => 'required|string|max:100|unique:user_roles,name',
            'description' => 'nullable|string|max:65535',
            // 'permissions' => 'nullable|array', // For assigning permissions later
            // 'permissions.*' => 'integer|exists:permissions,permission_id', // For assigning permissions later
        ];
    }
}