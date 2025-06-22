@extends('layouts.app')

@section('title', 'Bills')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Bills</h1>
        <a href="{{ route('bills.create') }}" class="btn btn-primary">Create New Bill</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Bill #</th>
                            <th>Supplier</th>
                            <th>Bill Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Amount Due</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td><a href="{{ route('bills.show', $bill) }}">{{ $bill->bill_number }}</a></td>
                                <td><a href="{{ route('suppliers.show', $bill->supplier) }}">{{ $bill->supplier->name }}</a></td>
                                <td>{{ $bill->bill_date->format('Y-m-d') }}</td>
                                <td>{{ $bill->due_date->format('Y-m-d') }}</td>
                                <td class="text-end">${{ number_format($bill->total_amount, 2) }}</td>
                                <td class="text-end">${{ number_format($bill->amount_due, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($bill->status) {
                                            'Paid' => 'bg-success',
                                            'Awaiting Payment' => 'bg-info text-dark',
                                            'Partially Paid' => 'bg-warning text-dark',
                                            'Cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $bill->status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('bills.edit', $bill) }}" class="btn btn-sm btn-warning">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No bills found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $bills->links() }}
            </div>
        </div>
    </div>
</div>
@endsection