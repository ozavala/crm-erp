@extends('layouts.app')

@section('title', 'Customer: ' . $customer->full_name)

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">{{ $customer->full_name }}</h1>
            <p class="text-muted mb-0">{{ $customer->company_name }}</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6 me-2">{{ $customer->status }}</span>
            <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to Customers</a>
        </div>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabs --}}
    <ul class="nav nav-tabs" id="customerDetailsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">Payments</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Contacts</button>
        </li>
        {{-- Other tabs can be added here: Contacts, Opportunities, Orders, etc. --}}
    </ul>

    <div class="tab-content" id="customerDetailsTabContent">
        {{-- Details Tab --}}
        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <div class="card card-body border-top-0">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Primary Information</h5>
                        @if($customer->type === 'Company')
                            <p><strong>Company Name:</strong> {{ $customer->company_name }}</p>
                        @else
                            <p><strong>Name:</strong> {{ $customer->first_name }} {{ $customer->last_name }}</p>
                        @endif
                        <p><strong>Legal ID:</strong> {{ $customer->legal_id }}</p>
                        <p><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $customer->phone_number }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Addresses</h5>
                        @forelse($customer->addresses as $address)
                            <p>
                                <strong>{{ $address->address_name ?? 'Primary Address' }}:</strong><br>
                                {{ $address->street_address_line_1 }}<br>
                                @if($address->street_address_line_2){{ $address->street_address_line_2 }}<br>@endif
                                {{ $address->city }}, {{ $address->state_province }} {{ $address->postal_code }}
                            </p>
                        @empty
                            <p>No addresses on file.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Payments Tab --}}
        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
            <div class="card card-body border-top-0">
                <h5>Payment History</h5>
                @if($payments->isEmpty())
                    <p>No payments have been recorded for this customer.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Paid For</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>
                                            @if ($payment->payable)
                                                @if ($payment->payable instanceof \App\Models\Order)
                                                    <a href="{{ route('orders.show', $payment->payable->order_id) }}">
                                                        Order #{{ $payment->payable->order_number }}
                                                    </a>
                                                @elseif ($payment->payable instanceof \App\Models\Invoice)
                                                    <a href="{{ route('invoices.show', $payment->payable->invoice_id) }}">
                                                        Invoice #{{ $payment->payable->invoice_number }}
                                                    </a>
                                                @else
                                                    {{ class_basename($payment->payable) }} #{{ $payment->payable->getKey() }}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $payment->createdBy->full_name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>