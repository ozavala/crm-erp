@extends('layouts.app')

@section('title', __('goods_receipts.Goods Receipt Details'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('goods_receipts.Goods Receipt') }} #{{ $goodsReceipt->goods_receipt_id }}</h1>
        <div>
            <a href="{{ route('purchase-orders.show', $goodsReceipt->purchaseOrder) }}" class="btn btn-secondary">{{ __('goods_receipts.Back to Purchase Order') }}</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">{{ __('goods_receipts.Receipt Details') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('goods_receipts.Purchase Order') }}:</strong> <a href="{{ route('purchase-orders.show', $goodsReceipt->purchaseOrder) }}">{{ $goodsReceipt->purchaseOrder->purchase_order_number }}</a></p>
                    <p><strong>{{ __('goods_receipts.Receipt Date') }}:</strong> {{ $goodsReceipt->receipt_date->format('Y-m-d') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('goods_receipts.Received By') }}:</strong> {{ $goodsReceipt->receivedBy->full_name ?? __('N/A') }}</p>
                    <p><strong>{{ __('goods_receipts.Notes') }}:</strong> {{ $goodsReceipt->notes ?: __('N/A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">{{ __('goods_receipts.Items Received') }}</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('goods_receipts.Product') }}</th>
                        <th>{{ __('goods_receipts.Description') }}</th>
                        <th class="text-end">{{ __('goods_receipts.Quantity Received') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goodsReceipt->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? __('N/A') }}</td>
                        <td>{{ $item->product->description ?? __('N/A') }}</td>
                        <td class="text-end">{{ $item->quantity_received }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

