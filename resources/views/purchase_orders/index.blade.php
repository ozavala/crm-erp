@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('purchase_orders.title') }}</h1>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">{{ __('purchase_orders.add_new_purchase_order') }}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('purchase-orders.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('purchase_orders.search_placeholder') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type_filter" class="form-select">
                        <option value="">{{ __('purchase_orders.all_types') }}</option>
                        @foreach($types as $key => $value)
                            <option value="{{ $key }}" {{ request('type_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status_filter" class="form-select">
                        <option value="">{{ __('purchase_orders.all_statuses') }}</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary flex-grow-1">{{ __('purchase_orders.filter') }}</button>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary ms-2" title="{{ __('purchase_orders.clear_filters') }}"><i class="bi bi-x-lg"></i> {{ __('purchase_orders.clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('purchase_orders.id') }}</th>
                <th>{{ __('purchase_orders.po_number') }}</th>
                <th>{{ __('purchase_orders.supplier') }}</th>
                <th>{{ __('purchase_orders.type') }}</th>
                <th>{{ __('purchase_orders.status') }}</th>
                <th class="text-end">{{ __('purchase_orders.total') }}</th>
                <th class="text-end">{{ __('purchase_orders.amount_paid') }}</th>
                <th class="text-end">{{ __('purchase_orders.amount_due') }}</th>
                <th>{{ __('purchase_orders.order_date') }}</th>
                <th>{{ __('purchase_orders.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchaseOrders as $purchaseOrder)
                <tr>
                    <td>{{ $purchaseOrder->purchase_order_id }}</td>
                    <td><a href="{{ route('purchase-orders.show', $purchaseOrder->purchase_order_id) }}">{{ $purchaseOrder->purchase_order_number ?: ('PO #'.$purchaseOrder->purchase_order_id) }}</a></td>
                    <td>{{ $purchaseOrder->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $purchaseOrder->type ?: 'N/A' }}</td>
                    <td>
                         @php
                            $statusClass = match($purchaseOrder->status) {
                                'Completed', 'Received' => 'bg-success',
                                'Sent', 'Confirmed' => 'bg-info text-dark',
                                'Partially Received' => 'bg-warning text-dark',
                                'Cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $purchaseOrder->status }}</span>
                    </td>
                    <td class="text-end">${{ number_format($purchaseOrder->total_amount, 2) }}</td>
                    <td class="text-end">${{ number_format($purchaseOrder->amount_paid, 2) }}</td>
                    <td class="text-end">${{ number_format($purchaseOrder->amount_due, 2) }}</td>
                    <td>{{ $purchaseOrder->order_date->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder->purchase_order_id) }}" class="btn btn-warning btn-sm">{{ __('purchase_orders.edit') }}</a>
                        <form action="{{ route('purchase-orders.destroy', $purchaseOrder->purchase_order_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('purchase_orders.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">{{ __('purchase_orders.no_purchase_orders_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $purchaseOrders->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection