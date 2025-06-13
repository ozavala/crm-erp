@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Order: {{ $order->order_number ?: ('Order #'.$order->order_id) }} <span class="badge bg-primary fs-6">{{ $order->status }}</span></h1>
        {{-- Add PDF export button or other actions here --}}
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Order ID: {{ $order->order_id }} | Date: {{ $order->order_date->format('M d, Y') }}
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Customer Details</h5>
                    <p><strong>Customer:</strong> <a href="{{ route('customers.show', $order->customer_id) }}">{{ $order->customer->full_name }}</a></p>
                    <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $order->customer->phone_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Related Entities</h5>
                    <p><strong>Quotation:</strong> 
                        @if($order->quotation)
                            <a href="{{ route('quotations.show', $order->quotation_id) }}">{{ $order->quotation->subject }}</a>
                        @else N/A @endif
                    </p>
                    <p><strong>Opportunity:</strong> 
                        @if($order->opportunity)
                            <a href="{{ route('opportunities.show', $order->opportunity_id) }}">{{ $order->opportunity->name }}</a>
                        @else N/A @endif
                    </p>
                    <p><strong>Created By:</strong> {{ $order->createdBy->full_name ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- @if($order->shippingAddress || $order->billingAddress) --}}
            {{-- This part needs to be adjusted if you implement Address model linking properly --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Shipping Address</h5>
                    @if($order->shipping_address_id && $shippingAddress = \App\Models\Address::find($order->shipping_address_id))
                        <p>
                            {{ $shippingAddress->street_address_line_1 }}<br>
                            @if($shippingAddress->street_address_line_2){{ $shippingAddress->street_address_line_2 }}<br>@endif
                            {{ $shippingAddress->city }}, {{ $shippingAddress->state_province }} {{ $shippingAddress->postal_code }}<br>
                            {{ $shippingAddress->country_code }}
                        </p>
                    @else
                        <p>N/A</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5>Billing Address</h5>
                     @if($order->billing_address_id && $billingAddress = \App\Models\Address::find($order->billing_address_id))
                        <p>
                            {{ $billingAddress->street_address_line_1 }}<br>
                            @if($billingAddress->street_address_line_2){{ $billingAddress->street_address_line_2 }}<br>@endif
                            {{ $billingAddress->city }}, {{ $billingAddress->state_province }} {{ $billingAddress->postal_code }}<br>
                            {{ $billingAddress->country_code }}
                        </p>
                    @else
                        <p>N/A</p>
                    @endif
                </div>
            </div>
            {{-- @endif --}}

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
                    @foreach($order->items as $index => $item)
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
                    <p class="d-flex justify-content-between"><span>Subtotal:</span> <span>${{ number_format($order->subtotal, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Discount:</span> <span>${{ number_format($order->discount_amount, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Tax:</span> <span>${{ number_format($order->tax_amount, 2) }}</span></p>
                    <h5 class="d-flex justify-content-between"><span>Total:</span> <span>${{ number_format($order->total_amount, 2) }}</span></h5>
                </div>
            </div>

            @if($order->notes)
                <hr>
                <h5>Notes</h5>
                <p>{{ nl2br(e($order->notes)) }}</p>
            @endif

        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('orders.edit', $order->order_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('orders.destroy', $order->order_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                {{-- Add button to create Invoice later --}}
            </div>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection