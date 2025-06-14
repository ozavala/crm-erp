@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Invoices</h1>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">Add New Invoice</a>
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
        <form action="{{ route('invoices.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by invoice #, customer, order..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status_filter" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary ms-2" title="Clear Filters"><i class="bi bi-x-lg"></i> Clear</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Invoice #</th>
                <th>Customer</th>
                <th>Order #</th>
                <th>Status</th>
                <th>Total</th>
                <th>Amount Due</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_id }}</td>
                    <td><a href="{{ route('invoices.show', $invoice->invoice_id) }}">{{ $invoice->invoice_number }}</a></td>
                    <td>{{ $invoice->customer->full_name ?? 'N/A' }}</td>
                    <td>{{ $invoice->order->order_number ?? 'N/A' }}</td>
                    <td><span class="badge {{ match($invoice->status) {'Paid' => 'bg-success', 'Partially Paid' => 'bg-warning text-dark', 'Overdue' => 'bg-danger', default => 'bg-secondary'} }}">{{ $invoice->status }}</span></td>
                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                    <td>${{ number_format($invoice->amount_due, 2) }}</td>
                    <td>{{ $invoice->due_date->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        {{-- Delete form is on show page for invoices due to payment checks --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No invoices found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $invoices->links() }}
    </div>
</div>
@endsection