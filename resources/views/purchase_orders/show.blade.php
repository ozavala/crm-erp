@extends('layouts.app')

@section('title', __('purchase_orders.details') . ' - ' . $purchaseOrder->purchase_order_number)

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">{{ __('purchase_orders.purchase_order') }}</h1>
            <p class="text-muted mb-0">{{ $purchaseOrder->purchase_order_number }}</p>
        </div>
        <div>
            {{-- Status Badge --}}
            @php
                $statusClass = match($purchaseOrder->status) {
                    'Completed', 'Received', 'Paid' => 'bg-success',
                    'Sent', 'Confirmed' => 'bg-info text-dark',
                    'Partially Received', 'Partially Paid' => 'bg-warning text-dark',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $statusClass }} fs-6 me-2">{{ $purchaseOrder->status }}</span>
            <a href="{{ route('purchase-orders.edit', $purchaseOrder->purchase_order_id) }}" class="btn btn-warning">{{ __('purchase_orders.edit') }}</a>
            <a href="{{ route('purchase-orders.print', $purchaseOrder->purchase_order_id) }}" class="btn btn-secondary" target="_blank">{{ __('purchase_orders.print_pdf') }}</a>
        </div>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Main Info Row --}}
    <div class="row">
        <div class="col-md-8">
            {{-- Main Details Card --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('purchase_orders.supplier') }}</h5>
                            <p>
                                <a href="{{ route('suppliers.show', $purchaseOrder->supplier->supplier_id) }}">{{ $purchaseOrder->supplier->name }}</a><br>
                                {{-- Supplier Address --}}
                                @if($supplierAddress = $purchaseOrder->supplier->addresses->first())
                                    {{ $supplierAddress->street_address_line_1 }}<br>
                                    @if($supplierAddress->street_address_line_2){{ $supplierAddress->street_address_line_2 }}<br>@endif
                                    {{ $supplierAddress->city }}, {{ $supplierAddress->state_province }} {{ $supplierAddress->postal_code }}
                                @else
                                    {{ __('purchase_orders.no_address_on_file') }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('purchase_orders.ship_to') }}</h5>
                            <p>
                                @if($purchaseOrder->shippingAddress)
                                    <strong>{{ $purchaseOrder->shippingAddress->address_name ?? __('purchase_orders.main_warehouse') }}</strong><br>
                                    {{ $purchaseOrder->shippingAddress->street_address_line_1 }}<br>
                                    @if($purchaseOrder->shippingAddress->street_address_line_2){{ $purchaseOrder->shippingAddress->street_address_line_2 }}<br>@endif
                                    {{ $purchaseOrder->shippingAddress->city }}, {{ $purchaseOrder->shippingAddress->state_province }} {{ $purchaseOrder->shippingAddress->postal_code }}
                                @else
                                    {{ __('purchase_orders.no_shipping_address_specified') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>{{ __('purchase_orders.order_date') }}</strong>: {{ $purchaseOrder->order_date->format('M d, Y') }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('purchase_orders.expected_delivery') }}</strong>: {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : __('purchase_orders.na') }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('purchase_orders.created_by') }}</strong>: {{ $purchaseOrder->createdBy->full_name ?? __('purchase_orders.na') }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- Tabs for Items, Landed Costs, etc. --}}
            <ul class="nav nav-tabs" id="poDetailsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="true">{{ __('purchase_orders.items') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="landed-costs-tab" data-bs-toggle="tab" data-bs-target="#landed-costs" type="button" role="tab" aria-controls="landed-costs" aria-selected="false">{{ __('purchase_orders.landed_costs') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="receipts-tab" data-bs-toggle="tab" data-bs-target="#receipts" type="button" role="tab" aria-controls="receipts" aria-selected="false">{{ __('purchase_orders.goods_receipts') }}</button>
                </li>
                 <li class="nav-item" role="presentation">
                    <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">{{ __('purchase_orders.payments') }}</button>
                </li>
            </ul>
            <div class="tab-content" id="poDetailsTabContent">
                {{-- Items Tab --}}
                <div class="tab-pane fade show active" id="items" role="tabpanel" aria-labelledby="items-tab">
                    <div class="card card-body border-top-0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('purchase_orders.item') }}</th>
                                    <th class="text-end">{{ __('purchase_orders.qty') }}</th>
                                    <th class="text-end">{{ __('purchase_orders.unit_price') }}</th>
                                    <th class="text-end">{{ __('purchase_orders.landed_cost_per_unit') }}</th>
                                    <th class="text-end">{{ __('purchase_orders.final_cost_per_unit') }}</th>
                                    <th class="text-end">{{ __('purchase_orders.line_total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->item_name }}</strong><br>
                                        <small class="text-muted">{{ $item->item_description }}</small>
                                    </td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end text-info">${{ number_format($item->landed_cost_per_unit, 4) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($item->unit_price + $item->landed_cost_per_unit, 4) }}</td>
                                    <td class="text-end">${{ number_format($item->item_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                                {{-- Landed Costs Tab --}}
                <div class="tab-pane fade" id="landed-costs" role="tabpanel" aria-labelledby="landed-costs-tab">
                    <div class="card card-body border-top-0">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>{{ __('purchase_orders.existing_costs') }}</h5>
                                    @if($purchaseOrder->landedCosts->isNotEmpty())
                                        <form action="{{ route('landed-costs.apportion', $purchaseOrder->purchase_order_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">{{ __('purchase_orders.apportion_costs') }}</button>
                                        </form>
                                    @endif
                                </div>
                                @if($purchaseOrder->landedCosts->isNotEmpty())
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('purchase_orders.description') }}</th>
                                                <th class="text-end">{{ __('purchase_orders.amount') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchaseOrder->landedCosts as $cost)
                                            <tr>
                                                <td>{{ $cost->description }}</td>
                                                <td class="text-end">${{ number_format($cost->amount, 2) }}</td>
                                                <td class="text-end">
                                                    <form action="{{ route('landed-costs.destroy', $cost->landed_cost_id) }}" method="POST" onsubmit="return confirm('{{ __('purchase_orders.confirm_delete') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('purchase_orders.delete') }}</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-end"><strong>{{ __('purchase_orders.total') }}:</strong></td>
                                                <td class="text-end"><strong>${{ number_format($purchaseOrder->landedCosts->sum('amount'), 2) }}</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                @else
                                    <p>{{ __('purchase_orders.no_landed_costs_added') }}</p>
                                @endif
                            </div>
                            <div class="col-md-5">
                                <h5>{{ __('purchase_orders.add_new_cost') }}</h5>
                                <form action="{{ route('landed-costs.store', $purchaseOrder->purchase_order_id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="description" class="form-label">{{ __('purchase_orders.description') }}</label>
                                        <input type="text" name="description" id="description" class="form-control" required placeholder="{{ __('purchase_orders.e_g_freight_duties') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">{{ __('purchase_orders.amount') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ __('purchase_orders.add_cost') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                                {{-- Goods Receipts Tab --}}
                <div class="tab-pane fade" id="receipts" role="tabpanel" aria-labelledby="receipts-tab">
                    <div class="card card-body border-top-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>{{ __('purchase_orders.goods_receipt_history') }}</h5>
                            @if(!in_array($purchaseOrder->status, ['Received', 'Completed', 'Cancelled', 'Paid']))
                                <a href="{{ route('goods-receipts.create', $purchaseOrder->purchase_order_id) }}" class="btn btn-sm btn-primary">{{ __('purchase_orders.create_goods_receipt') }}</a>
                            @endif
                        </div>
                        @if($purchaseOrder->goodsReceipts->isNotEmpty())
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('purchase_orders.receipt_number') }}</th>
                                        <th>{{ __('purchase_orders.receipt_date') }}</th>
                                        <th>{{ __('purchase_orders.received_by') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->goodsReceipts as $receipt)
                                    <tr>
                                        <td><a href="{{ route('goods-receipts.show', $receipt->goods_receipt_id) }}">GR-{{ str_pad($receipt->goods_receipt_id, 6, '0', STR_PAD_LEFT) }}</a></td>
                                        <td>{{ $receipt->receipt_date->format('M d, Y') }}</td>
                                        <td>{{ $receipt->receivedBy->full_name ?? __('purchase_orders.na') }}</td>
                                        <td class="text-end"><a href="{{ route('goods-receipts.show', $receipt->goods_receipt_id) }}" class="btn btn-info btn-sm">{{ __('purchase_orders.view') }}</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>{{ __('purchase_orders.no_goods_received_for_this_order') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Payments Tab --}}
                <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                    <div class="card card-body border-top-0">
                        <div class="row">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                @include('partials._payment_form', [
                                    'payable' => $purchaseOrder,
                                    'form_url' => route('purchase-orders.payments.store', $purchaseOrder->purchase_order_id)
                                ])
                            </div>
                            <div class="col-lg-6">
                                @include('partials._payment_list', ['payments' => $purchaseOrder->payments])
                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- End Tab Content --}}
        </div> {{-- End col-md-8 --}}

        {{-- Right Column for Financials --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    {{ __('purchase_orders.financials') }}
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ __('purchase_orders.subtotal') }}
                        <span>${{ number_format($purchaseOrder->subtotal, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ __('purchase_orders.tax') }} ({{ $purchaseOrder->tax_percentage ?? 0 }}%)
                        <span>${{ number_format($purchaseOrder->tax_amount, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ __('purchase_orders.shipping') }}
                        <span>${{ number_format($purchaseOrder->shipping_cost, 2) }}</span>
                    </li>
                    @if($purchaseOrder->other_charges > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ __('purchase_orders.other_charges') }}
                        <span>${{ number_format($purchaseOrder->other_charges, 2) }}</span>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                        {{ __('purchase_orders.total_amount') }}
                        <span>${{ number_format($purchaseOrder->total_amount, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                        {{ __('purchase_orders.amount_paid') }}
                        <span>${{ number_format($purchaseOrder->amount_paid, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold text-danger">
                        {{ __('purchase_orders.amount_due') }}
                        <span>${{ number_format($purchaseOrder->amount_due, 2) }}</span>
                    </li>
                </ul>
                <div class="card-body">
                    <h5 class="card-title">{{ __('purchase_orders.notes') }}</h5>
                    <p class="card-text">{{ $purchaseOrder->notes ?? __('purchase_orders.no_notes_for_this_purchase_order') }}</p>
                </div>
            </div>
        </div>
    </div> {{-- End Main Row --}}
</div> {{-- End Container --}}
@endsection
