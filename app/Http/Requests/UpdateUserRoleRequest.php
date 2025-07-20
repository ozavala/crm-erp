<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roleId = $this->route('user_role') ? $this->route('user_role')->role_id : null;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('user_roles', 'name')->ignore($roleId, 'role_id'),
            ],
            'description' => 'nullable|string|max:65535',
            'permissions' => 'nullable|array', // For assigning permissions later
            'permissions.*' => 'integer|exists:permissions,permission_id', // For assigning permissions later
        ];
    }
}