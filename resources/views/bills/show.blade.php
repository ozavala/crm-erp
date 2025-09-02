@extends('layouts.app')

@section('title', __('bills.Bill Details'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Bill #{{ $bill->bill_number }}
            @php
                $statusClass = match($bill->status) {
                    'Paid' => 'bg-success',
                    'Awaiting Payment' => 'bg-info text-dark',
                    'Partially Paid' => 'bg-warning text-dark',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $statusClass }} fs-6">{{ $bill->status }}</span>
        </h1>
    </div>
     @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="mb-3">
        <a href="{{ route('bills.edit', $bill) }}" class="btn btn-warning">Edit Bill</a>
        <a href="{{ route('bills.pdf', $bill->bill_id) }}" class="btn btn-info" target="_blank">Download PDF</a>
        {{-- Add delete button here later --}}
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Bill ID: {{ $bill->bill_id }} | Bill Date: {{ $bill->bill_date->format('M d, Y') }} | Due Date: {{ $bill->due_date->format('M d, Y') }}
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Supplier Details</h5>
                    <p><strong>Supplier:</strong> <a href="{{ route('suppliers.show', $bill->supplier_id) }}">{{ $bill->supplier->name }}</a></p>
                    @if($bill->purchaseOrder)
                        <p><strong>From Purchase Order:</strong> <a href="{{ route('purchase-orders.show', $bill->purchase_order_id) }}">{{ $bill->purchaseOrder->purchase_order_number }}</a></p>
                    @endif
                </div>
            </div>

            <h5>Line Items</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">${{ number_format($item->item_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row justify-content-end mt-3">
                <div class="col-md-4">
                    <p class="d-flex justify-content-between"><span>Subtotal:</span> <span>${{ number_format($bill->subtotal, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Tax:</span> <span>${{ number_format($bill->tax_amount, 2) }}</span></p>
                    <hr class="my-1">
                    <p class="d-flex justify-content-between"><span>Total Amount:</span> <span>${{ number_format($bill->total_amount, 2) }}</span></p>
                    <p class="d-flex justify-content-between"><span>Amount Paid:</span> <span>${{ number_format($bill->amount_paid, 2) }}</span></p>
                    <h5 class="d-flex justify-content-between"><span>Amount Due:</span> <span>${{ number_format($bill->amount_due, 2) }}</span></h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments Section --}}
    <div class="card mt-4">
        <div class="card-header"><h4>Payments</h4></div>
        <div class="card-body">
            @if ($bill->amount_due > 0 && $bill->status !== 'Cancelled')
            <div class="mb-4 p-3 border rounded">
                <h5>Record New Payment</h5>
                <form action="{{ route('payments.store') }}" method="POST">
                    @include('payments._form', ['payable' => $bill])
                </form>
            </div>
            @endif

            <h5>Payment History</h5>
            @if($bill->payments->isNotEmpty())
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($bill->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method ?: 'N/A' }}</td>
                            <td>{{ $payment->reference_number ?: 'N/A' }}</td>
                            <td>
                                <form action="{{ route('payments.destroy', $payment->payment_id) }}" method="POST" onsubmit="return confirm('{{ __('bills.Delete this payment?') }}');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No payments recorded for this bill yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection