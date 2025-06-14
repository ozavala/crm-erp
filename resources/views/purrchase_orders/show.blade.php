@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1>PO: {{ $purchaseOrder->purchase_order_number ?: ('PO #'.$purchaseOrder->purchase_order_id) }}
            <span class="badge bg-info text-dark fs-6">{{ $purchaseOrder->status }}</span>
            @if($purchaseOrder->type) <span class="badge bg-secondary fs-6">{{ $purchaseOrder->type }}</span> @endif
        </h1>
        {{-- Add PDF export button or other actions here --}}
    </div>

    <div class="card mb-4">
        <div class="card-header">
            PO ID: {{ $purchaseOrder->purchase_order_id }} | Order Date: {{ $purchaseOrder->order_date->format('M d, Y') }}
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Supplier Details</h5>
                    <p><strong>Supplier:</strong> <a href="{{ route('suppliers.show', $purchaseOrder->supplier_id) }}">{{ $purchaseOrder->supplier->name }}</a></p>
                    <p><strong>Contact:</strong> {{ $purchaseOrder->supplier->contact_person ?: 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $purchaseOrder->supplier->email ?: 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Order Information</h5>
                    <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'N/A' }}</p>
                    <p><strong>Created By:</strong> {{ $purchaseOrder->createdBy->full_name ?? 'N/A' }}</p>
                </div>
            </div>

            @if($purchaseOrder->shippingAddress)
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5>Ship To Address (Your Company)</h5>
                    <p>
                        {{ $purchaseOrder->shippingAddress->street_address_line_1 }}<br>
                        @if($purchaseOrder->shippingAddress->street_address_line_2){{ $purchaseOrder->shippingAddress->street_address_line_2 }}<br>@endif
                        {{ $purchaseOrder->shippingAddress->city }}, {{ $purchaseOrder->shippingAddress->state_province }} {{ $purchaseOrder->shippingAddress->postal_code }}<br>
                        {{ $purchaseOrder->shippingAddress->country_code }}
                        ({{ $purchaseOrder->shippingAddress->address_type ?? 'Company Address'}})
                    </p>
                </div>
            </div>
            @endif

            <h5>Line Items</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }} @if($item->product) <small class="text-muted">(SKU: {{ $item->product->sku }})</small> @endif</td>
                        <td>{{ $item->item_description ?: 'N/A' }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">${{ number_format($item->item_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row justify-content-end mt-3">
                <div class="col-md-4">
                    <p class="d-flex justify-content-between"><span>Subtotal:</span> <span>${{ number_format($purchaseOrder->subtotal, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Discount:</span> <span>-${{ number_format($purchaseOrder->discount_amount, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Tax ({{ $purchaseOrder->tax_percentage ?: 0 }}%):</span> <span>${{ number_format($purchaseOrder->tax_amount, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Shipping Cost:</span> <span>${{ number_format($purchaseOrder->shipping_cost, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Other Charges:</span> <span>${{ number_format($purchaseOrder->other_charges, 2) }}</span></p>
                    <h5 class="d-flex justify-content-between"><span>Grand Total:</span> <span>${{ number_format($purchaseOrder->total_amount, 2) }}</span></h5>
                </div>
            </div>

            @if($purchaseOrder->terms_and_conditions)
                <hr>
                <h5>Terms & Conditions</h5>
                <p>{{ nl2br(e($purchaseOrder->terms_and_conditions)) }}</p>
            @endif

            @if($purchaseOrder->notes)
                <hr>
                <h5>Notes</h5>
                <p>{{ nl2br(e($purchaseOrder->notes)) }}</p>
            @endif

        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('purchase-orders.edit', $purchaseOrder->purchase_order_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('purchase-orders.destroy', $purchaseOrder->purchase_order_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                {{-- Add button to mark as received, etc. --}}
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection