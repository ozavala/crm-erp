<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
        /**$orderId = $this->route('order') ? $this->route('order')->order_id : null;*/
        /** @var \App\Models\Order|null $order */
        $order = $this->route('order');
        $orderId = $order ? $order->order_id : null;
        return [
            'customer_id' => 'required|exists:customers,customer_id',
            'quotation_id' => 'nullable|exists:quotations,quotation_id',
            'opportunity_id' => 'nullable|exists:opportunities,opportunity_id',
            'shipping_address_id' => 'nullable|exists:addresses,address_id',
            'billing_address_id' => 'nullable|exists:addresses,address_id',
            'order_number' => ['nullable', 'string', 'max:255', Rule::unique('orders', 'order_number')->ignore($orderId, 'order_id')],
            'order_date' => 'required|date',
            'status' => ['required', 'string', Rule::in(array_keys(Order::$statuses))],
            'notes' => 'nullable|string',
            'discount_type' => 'nullable|string|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            // Line items validation
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'nullable|integer|exists:order_items,order_item_id', // For existing items
            'items.*.product_id' => 'nullable|exists:products,product_id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}