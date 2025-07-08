@extends('layouts.app')

@section('title', __('bills.Bills'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('bills.Bills') }}</h1>
        <a href="{{ route('bills.create') }}" class="btn btn-primary">{{ __('bills.Create New Bill') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('bills.Bill #') }}</th>
                            <th>{{ __('bills.Supplier') }}</th>
                            <th>{{ __('bills.Bill Date') }}</th>
                            <th>{{ __('bills.Due Date') }}</th>
                            <th class="text-end">{{ __('bills.Total') }}</th>
                            <th class="text-end">{{ __('bills.Amount Due') }}</th>
                            <th>{{ __('bills.Status') }}</th>
                            <th>{{ __('bills.Actions') }}</th>
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
                                    <a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-info">{{ __('bills.View') }}</a>
                                    <a href="{{ route('bills.edit', $bill) }}" class="btn btn-sm btn-warning">{{ __('bills.Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('bills.No bills found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $bills->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection