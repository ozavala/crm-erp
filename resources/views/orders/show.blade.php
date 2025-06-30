@extends('layouts.app')

@section('title', 'Sales Order: ' . $order->order_number)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Sales Order <span class="text-muted">#{{ $order->order_number }}</span></h1>
        <div>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
            {{-- Add other actions like Print, Edit, etc. --}}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Display Order Details --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Order Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Customer:</strong> <a href="{{ route('customers.show', $order->customer_id) }}">{{ $order->customer->full_name }}</a></p>
                    <p><strong>Order Date:</strong> {{ $order->order_date->format('Y-m-d') }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-primary">{{ $order->status }}</span></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                    <p><strong>Amount Paid:</strong> ${{ number_format($order->amount_paid, 2) }}</p>
                    <p><strong>Amount Due:</strong> <span class="fw-bold">${{ number_format($order->amount_due, 2) }}</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Order Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->item_name }}</strong><br>
                                    <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">${{ number_format($item->item_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Payment Section --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            @include('partials._payment_form', [
                'payable' => $order,
                'form_url' => route('orders.payments.store', $order->order_id)
            ])
        </div>
        <div class="col-lg-6 mb-4">
            @include('partials._payment_list', ['payments' => $order->payments])
        </div>
    </div>
</div>
@endsection