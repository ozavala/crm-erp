@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('invoices.title') }}</h1>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">{{ __('invoices.add_new_invoice') }}</a>
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
                    <input type="text" name="search" class="form-control" placeholder="{{ __('invoices.search_placeholder') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status_filter" class="form-select">
                        <option value="">{{ __('invoices.all_statuses') }}</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">{{ __('invoices.filter') }}</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary ms-2" title="{{ __('invoices.clear_filters') }}"><i class="bi bi-x-lg"></i> {{ __('invoices.clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('invoices.id') }}</th>
                <th>{{ __('invoices.invoice_number') }}</th>
                <th>{{ __('invoices.customer') }}</th>
                <th>{{ __('invoices.order_number') }}</th>
                <th>{{ __('invoices.status') }}</th>
                <th>{{ __('invoices.total') }}</th>
                <th>{{ __('invoices.amount_due') }}</th>
                <th>{{ __('invoices.due_date') }}</th>
                <th>{{ __('invoices.actions') }}</th>
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
                        <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="btn btn-warning btn-sm">{{ __('invoices.edit') }}</a>
                        <a href="{{ route('invoices.create', ['clone_from' => $invoice->invoice_id]) }}" class="btn btn-secondary btn-sm" title="{{ __('invoices.clone_invoice') }}">
                            <i class="bi bi-copy"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">{{ __('invoices.no_invoices_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $invoices->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection