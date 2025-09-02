@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('purchase_orders.Purchase Order') }}: #{{ $purchaseOrder->purchase_order_id }}</h1>
        <div>
            <a href="{{ route('purchase-orders.print', $purchaseOrder) }}" class="btn btn-info" target="_blank">Print to PDF</a>
            @if(!in_array($purchaseOrder->status, ['Draft', 'Sent', 'Completed', 'Cancelled']))
                <a href="{{ route('goods-receipts.create', $purchaseOrder) }}" class="btn btn-primary">Receive Goods</a>
            @endif
            <a href="{{ route('purchase-orders.edit', $purchaseOrder->purchase_order_id) }}" class="btn btn-warning">{{ __('messages.Edit') }}</a>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">{{ __('messages.Back to List') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">PO Details</div>
                <div class="card-body">
                    <p><strong>{{ __('purchase_orders.Supplier') }}:</strong> {{ $purchaseOrder->supplier->name }}</p>
                    <p><strong>{{ __('messages.Date') }}:</strong> {{ $purchaseOrder->order_date }}</p>
                    <p><strong>{{ __('messages.Status') }}:</strong> {{ $purchaseOrder->status }}</p>
                    <p><strong>{{ __('messages.Total') }}:</strong> {{ $purchaseOrder->total }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Shipping Information</div>
                <div class="card-body">
                    @if($purchaseOrder->shippingAddress)
                        <p><strong>Shipping To:</strong></p>
                        <address>
                            {{ $purchaseOrder->shippingAddress->street_address_line_1 }}<br>
                            @if($purchaseOrder->shippingAddress->street_address_line_2){{ $purchaseOrder->shippingAddress->street_address_line_2 }}<br>@endif
                            {{ $purchaseOrder->shippingAddress->city }}, {{ $purchaseOrder->shippingAddress->state_province }} {{ $purchaseOrder->shippingAddress->postal_code }}<br>
                            {{ $purchaseOrder->shippingAddress->country }}
                        </address>
                    @else
                        <p>No shipping address specified.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Items</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Landed Cost/Unit</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->item_description }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">${{ $item->landed_cost_per_unit ? number_format($item->landed_cost_per_unit, 4) : 'N/A' }}</td>
                        <td class="text-end">${{ number_format($item->item_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                        <td class="text-end">${{ number_format($purchaseOrder->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Tax ({{ $purchaseOrder->tax_percentage ?? 0 }}%):</strong></td>
                        <td class="text-end">${{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Shipping:</strong></td>
                        <td class="text-end">${{ number_format($purchaseOrder->shipping_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end"><strong>${{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Landed Costs Management Card -->
    <div class="card mb-3">
        <div class="card-header">Landed Costs</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                    <h5>Existing Costs</h5>
                    @if($purchaseOrder->landedCosts->isNotEmpty())
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->landedCosts as $cost)
                                <tr>
                                    <td>{{ $cost->description }}</td>
                                    <td class="text-end">${{ number_format($cost->amount, 2) }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('landed-costs.destroy', $cost) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end"><strong>Total Landed Costs:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($purchaseOrder->landedCosts->sum('amount'), 2) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <p>No landed costs have been added yet.</p>
                    @endif
                </div>
                <div class="col-md-5">
                    <h5>Add New Cost</h5>
                    <form action="{{ route('landed-costs.store', $purchaseOrder) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('messages.Description') }} (e.g., Freight, Duties)</label>
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('messages.Amount') }}</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('messages.Add Cost') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <form action="{{ route('landed-costs.apportion', $purchaseOrder) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">{{ __('messages.Calculate & Apportion Costs to Items') }}</button>
            </form>
        </div>
    </div>
    <!-- End Landed Costs Card -->

    <!-- Goods Receipt History Card -->
    <div class="card mb-3">
        <div class="card-header">Goods Receipt History</div>
        <div class="card-body">
            @if($purchaseOrder->goodsReceipts->isNotEmpty())
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Receipt Date</th>
                            <th>Received By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->goodsReceipts as $receipt)
                        <tr>
                            <td><a href="{{ route('goods-receipts.show', $receipt) }}">GR-{{ $receipt->goods_receipt_id }}</a></td>
                            <td>{{ $receipt->receipt_date->format('Y-m-d') }}</td>
                            <td>{{ $receipt->receivedBy->full_name ?? 'N/A' }}</td>
                            <td class="text-end"><a href="{{ route('goods-receipts.show', $receipt) }}" class="btn btn-info btn-sm">{{ __('messages.View') }}</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>{{ __('messages.No goods have been received for this order yet.') }}</p>
            @endif
        </div>
    </div>
    <!-- End Goods Receipt History Card -->
</div>
@endsection