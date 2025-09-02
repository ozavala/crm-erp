<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
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
        $warehouseId = $this->route('warehouse') ? $this->route('warehouse')->warehouse_id : null;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('warehouses', 'name')->ignore($warehouseId, 'warehouse_id'),
            ],
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
        ];
    }
}