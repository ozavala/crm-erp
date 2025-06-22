@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1>PO: {{ $purchaseOrder->purchase_order_number }}
            @php
                $statusClass = match($purchaseOrder->status) {
                    'Completed', 'Received' => 'bg-success',
                    'Sent', 'Confirmed' => 'bg-info text-dark',
                    'Partially Received' => 'bg-warning text-dark',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $statusClass }} fs-6">{{ $purchaseOrder->status }}</span>
        </h1>
        {{-- Add PDF export button or other actions here --}}
    </div>
     @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error')) {{-- For payment specific errors --}}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            PO ID: {{ $purchaseOrder->purchase_order_id }} | Order Date: {{ $purchaseOrder->order_date->format('M d, Y') }} | Expected Delivery: {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'N/A' }}
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Supplier Details</h5>
                    <p><strong>Supplier:</strong> <a href="{{ route('suppliers.show', $purchaseOrder->supplier_id) }}">{{ $purchaseOrder->supplier->name }}</a></p>
                    <p><strong>Contact:</strong> {{ $purchaseOrder->supplier->contact_person ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $purchaseOrder->supplier->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Shipping & Other Info</h5>
                    <p><strong>Shipping To:</strong>
                        @if($purchaseOrder->shippingAddress)
                            {{ $purchaseOrder->shippingAddress->street_address_line_1 }}, {{ $purchaseOrder->shippingAddress->city }}
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Type:</strong> {{ $purchaseOrder->type }}</p>
                    <p><strong>Created By:</strong> {{ $purchaseOrder->createdBy->full_name ?? 'N/A' }}</p>
                </div>
            </div>

            <h5>Line Items</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }} @if($item->product) <small class="text-muted">({{ $item->product->sku }})</small> @endif</td>
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
                    <hr class="my-1">
                    <p class="d-flex justify-content-between"><span>Total Amount:</span> <span>${{ number_format($purchaseOrder->total_amount, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Amount Paid:</span> <span>${{ number_format($purchaseOrder->amount_paid, 2) }}</span></p>
                    <h5 class="d-flex justify-content-between"><span>Amount Due:</span> <span>${{ number_format($purchaseOrder->amount_due, 2) }}</span></h5>
                </div>
            </div>

            @if($purchaseOrder->notes)
                <hr><h5>Notes:</h5><p>{{ nl2br(e($purchaseOrder->notes)) }}</p>
            @endif
            @if($purchaseOrder->terms_and_conditions)
                <hr><h5>Terms & Conditions:</h5><p>{{ nl2br(e($purchaseOrder->terms_and_conditions)) }}</p>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('purchase-orders.edit', $purchaseOrder->purchase_order_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('purchase-orders.destroy', $purchaseOrder->purchase_order_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    {{-- Bills & Payments Section for Purchase Order --}}
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Bills & Payments</h4>
            <a href="{{ route('bills.create', ['purchase_order_id' => $purchaseOrder->purchase_order_id]) }}" class="btn btn-primary">Create Bill from PO</a>
        </div>
        <div class="card-body">
            {{-- This section now shows Bills related to the PO, not direct payments --}}
            <h5>Associated Bills</h5>
            @if($purchaseOrder->bills->isNotEmpty())
                <ul class="list-group">
                    @foreach($purchaseOrder->bills as $bill)
                        <li class="list-group-item"><a href="{{ route('bills.show', $bill) }}">Bill #{{ $bill->bill_number }}</a> - ${{ number_format($bill->total_amount, 2) }} ({{ $bill->status }})</li>
                    @endforeach
                </ul>
            @else
                <p>No bills have been created for this purchase order yet.</p>
            @endif

            <h5>Payment History</h5>
            @if($purchaseOrder->payments->isNotEmpty())
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($purchaseOrder->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method ?: 'N/A' }}</td>
                            <td>{{ $payment->reference_number ?: 'N/A' }}</td>
                            <td>
                                @if(!in_array($purchaseOrder->status, ['Completed', 'Cancelled']))
                                <form action="{{ route('payments.destroy', $payment->payment_id) }}" method="POST" onsubmit="return confirm('Delete this payment?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No payments recorded for this purchase order yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection