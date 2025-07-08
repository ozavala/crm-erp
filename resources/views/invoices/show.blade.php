@extends('layouts.app')

@section('title', __('invoices.details') . ': ' . $invoice->invoice_number)

@section('content')
<div class="container">
   
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>{{ __('invoices.invoice') }} <span class="text-muted">#{{ $invoice->invoice_number }}</span></h1>
            <p class="lead">{{ __('invoices.for_customer') }}: <a href="{{ route('customers.show', $invoice->customer->customer_id) }}">{{ $invoice->customer->full_name }}</a></p>
        </div>
        <div>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">{{ __('invoices.back_to_invoices') }}</a>
            <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="btn btn-warning">{{ __('invoices.edit') }}</a>
            <a href="{{ route('invoices.pdf', $invoice->invoice_id) }}" class="btn btn-info" target="_blank">{{ __('invoices.download_pdf') }}</a>
            @if($invoice->status === 'Overdue' && $invoice->amount_due > 0)
                <form action="{{ route('invoices.sendReminder', $invoice->invoice_id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">{{ __('invoices.send_reminder') }}</button>
                </form>
            @endif
        </div>
    </div>

    
    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

            
    {{-- Details --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('invoices.details') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('invoices.customer') }}:</strong> <a href="{{ route('customers.show', $invoice->customer->customer_id) }}">{{ $invoice->customer->full_name }}</a></p>
                    @if($invoice->order)
                    <p><strong>{{ __('invoices.from_order') }}:</strong> <a href="{{ route('orders.show', $invoice->order->order_id) }}">#{{ $invoice->order->order_number }}</a></p>
                    @endif
                    @if($invoice->quotation)
                    <p><strong>{{ __('invoices.from_quotation') }}:</strong> <a href="{{ route('quotations.show', $invoice->quotation->quotation_id) }}">{{ $invoice->quotation->subject }}</a></p>
                    @endif
                    <p><strong>{{ __('invoices.status') }}:</strong> <span class="badge {{ match($invoice->status) {'Paid' => 'bg-success', 'Partially Paid' => 'bg-warning text-dark', 'Overdue' => 'bg-danger', default => 'bg-secondary'} }}">{{ $invoice->status }}</span></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>{{ __('invoices.invoice_date') }}:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</p>
                    <p><strong>{{ __('invoices.due_date') }}:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
    </div>

        
    {{-- Items and Totals --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('invoices.items') }}</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('invoices.item') }}</th>
                        <th class="text-end">{{ __('invoices.quantity') }}</th>
                        <th class="text-end">{{ __('invoices.unit_price') }}</th>
                        <th class="text-end">{{ __('invoices.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->item_name }}</strong><br>
                                <small class="text-muted">{{ $item->item_description }}</small>
                            </td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">${{ number_format($item->item_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>{{ __('invoices.subtotal') }}</strong></td>
                        <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                    <tr>
                        <td colspan="3" class="text-end"><strong>{{ __('invoices.discount') }}</strong></td>
                        <td class="text-end text-danger">- ${{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="text-end"><strong>{{ __('invoices.tax') }} ({{ $invoice->tax_percentage }}%)</strong></td>
                        <td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end"><strong>{{ __('invoices.total') }}</strong></td>
                        <td class="text-end">${{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Payments Section --}}
    @include('partials._payments', ['model' => $invoice])

    {{-- Notes & Terms --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('invoices.notes') }}</div>
                <div class="card-body">
                    <p>{{ $invoice->notes ?: __('invoices.no_notes') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('invoices.terms_and_conditions') }}</div>
                <div class="card-body">
                    <p>{{ $invoice->terms_and_conditions ?: __('invoices.no_terms') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
