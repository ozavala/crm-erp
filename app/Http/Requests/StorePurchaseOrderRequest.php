<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\PurchaseOrder;

class StorePurchaseOrderRequest extends FormRequest
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
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'shipping_address_id' => 'nullable|exists:addresses,address_id', // Your company's shipping address
            'purchase_order_number' => ['nullable', 'string', 'max:255', Rule::unique('purchase_orders', 'purchase_order_number')],
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'type' => ['nullable', 'string', Rule::in(array_keys(PurchaseOrder::$types))],
            'status' => ['required', 'string', Rule::in(array_keys(PurchaseOrder::$statuses))],
            'notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'discount_type' => 'nullable|string|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'shipping_cost' => 'nullable|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
            // Line items validation
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,product_id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
