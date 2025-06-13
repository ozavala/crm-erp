<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,customer_id',
            'quotation_id' => 'nullable|exists:quotations,quotation_id',
            'opportunity_id' => 'nullable|exists:opportunities,opportunity_id',
            'shipping_address_id' => 'nullable|exists:addresses,address_id', // Assuming 'addresses' table
            'billing_address_id' => 'nullable|exists:addresses,address_id',  // Assuming 'addresses' table
            'order_number' => 'nullable|string|max:255|unique:orders,order_number',
            'order_date' => 'required|date',
            'status' => ['required', 'string', Rule::in(array_keys(Order::$statuses))],
            'notes' => 'nullable|string',
            'discount_type' => 'nullable|string|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
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