<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
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
            'order_id' => 'nullable|exists:orders,order_id',
            'quotation_id' => 'nullable|exists:quotations,quotation_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'invoice_number' => ['nullable', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')],
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => ['required', 'string', Rule::in(array_keys(Invoice::$statuses))],
            'terms_and_conditions' => 'nullable|string',
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
            // amount_paid will be handled by payments
        ];
    }
}