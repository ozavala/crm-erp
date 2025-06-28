@extends('layouts.app')

@section('title', 'Goods Receipt Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Goods Receipt #{{ $goodsReceipt->goods_receipt_id }}</h1>
        <div>
            <a href="{{ route('purchase-orders.show', $goodsReceipt->purchaseOrder) }}" class="btn btn-secondary">Back to Purchase Order</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Receipt Details</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Purchase Order:</strong> <a href="{{ route('purchase-orders.show', $goodsReceipt->purchaseOrder) }}">{{ $goodsReceipt->purchaseOrder->purchase_order_number }}</a></p>
                    <p><strong>Receipt Date:</strong> {{ $goodsReceipt->receipt_date->format('Y-m-d') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Received By:</strong> {{ $goodsReceipt->receivedBy->full_name ?? 'N/A' }}</p>
                    <p><strong>Notes:</strong> {{ $goodsReceipt->notes ?: 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Items Received</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th class="text-end">Quantity Received</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goodsReceipt->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td>{{ $item->product->description ?? 'N/A' }}</td>
                        <td class="text-end">{{ $item->quantity_received }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

