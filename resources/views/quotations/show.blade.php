@extends('layouts.app')

@section('title', __('quotations.details') . ': ' . $quotation->subject)

@section('content')
<div class="container">
   
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>{{ __('quotations.quotation') }} <span class="text-muted">#{{ $quotation->quotation_id }}</span></h1>
            <p class="lead">{{ $quotation->subject }}</p>
        </div>
        <div>
            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">{{ __('quotations.back_to_quotations') }}</a>
            <a href="{{ route('quotations.edit', $quotation->quotation_id) }}" class="btn btn-warning">{{ __('quotations.edit') }}</a>
        </div>
    </div>

    
    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

            
    {{-- Action Buttons --}}
    <div class="card mb-4">
        <div class="card-header">
            {{ __('quotations.actions') }}
        </div>
        <div class="card-body d-flex gap-2">
            <form action="{{ route('quotations.sendEmail', $quotation->quotation_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">{{ __('quotations.send_email') }}</button>
            </form>
            @if(in_array($quotation->status, ['Draft', 'Sent']))
                <form action="{{ route('quotations.status.update', $quotation->quotation_id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="Accepted">
                    <button type="submit" class="btn btn-success">{{ __('quotations.mark_as_accepted') }}</button>
                </form>
                <form action="{{ route('quotations.status.update', $quotation->quotation_id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="Declined">
                    <button type="submit" class="btn btn-danger">{{ __('quotations.mark_as_declined') }}</button>
                </form>
            @elseif($quotation->status == 'Accepted')
                @if($quotation->invoice)
                    <a href="{{ route('invoices.show', $quotation->invoice->invoice_id) }}" class="btn btn-info">{{ __('quotations.view_invoice') }}</a>
                @else
                    <a href="{{ route('invoices.create', ['quotation_id' => $quotation->quotation_id]) }}" class="btn btn-primary">{{ __('quotations.create_invoice') }}</a>
                @endif
            @else
                <p class="mb-0">{{ __('quotations.no_further_actions') }} <strong>{{ $quotation->status }}</strong></p>
            @endif
        </div>
    </div>

            
    {{-- Details --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('quotations.details') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('quotations.customer') }}:</strong> <a href="{{ route('customers.show', $quotation->opportunity->customer->customer_id) }}">{{ $quotation->opportunity->customer->full_name }}</a></p>
                    <p><strong>{{ __('quotations.opportunity') }}:</strong> <a href="{{ route('opportunities.show', $quotation->opportunity->opportunity_id) }}">{{ $quotation->opportunity->name }}</a></p>
                    <p><strong>{{ __('quotations.status') }}:</strong> <span class="badge bg-primary">{{ $quotation->status }}</span></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>{{ __('quotations.quotation_date') }}:</strong> {{ $quotation->quotation_date->format('Y-m-d') }}</p>
                    <p><strong>{{ __('quotations.expiry_date') }}:</strong> {{ $quotation->expiry_date?->format('Y-m-d') ?? __('quotations.na') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <a href="{{ route('quotations.pdf', $quotation) }}" class="btn btn-outline-primary" target="_blank"><i class="bi bi-file-earmark-pdf"></i> {{ __('quotations.download_pdf') }}</a>
    </div>

        
    {{-- Items and Totals --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('quotations.items') }}</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('quotations.item') }}</th>
                        <th class="text-end">{{ __('quotations.quantity') }}</th>
                        <th class="text-end">{{ __('quotations.unit_price') }}</th>
                        <th class="text-end">{{ __('quotations.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->items as $item)
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
                        <td colspan="3" class="text-end"><strong>{{ __('quotations.subtotal') }}</strong></td>
                        <td class="text-end">${{ number_format($quotation->subtotal, 2) }}</td>
                    </tr>
                    @if($quotation->discount_amount > 0)
                    <tr>
                        <td colspan="3" class="text-end"><strong>{{ __('quotations.discount') }}</strong></td>
                        <td class="text-end text-danger">- ${{ number_format($quotation->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="text-end"><strong>{{ __('quotations.tax') }} ({{ $quotation->tax_percentage }}%)</strong></td>
                        <td class="text-end">${{ number_format($quotation->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end"><strong>{{ __('quotations.total') }}</strong></td>
                        <td class="text-end">${{ number_format($quotation->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
