@extends('layouts.app')

@section('title', __('goods_receipts.Receive Goods for PO') . ': ' . $purchaseOrder->purchase_order_number)

@section('content')
<div class="container">
    <h1>{{ __('goods_receipts.Receive Goods for PO') }}: <a href="{{ route('purchase-orders.show', $purchaseOrder) }}">{{ $purchaseOrder->purchase_order_number }}</a></h1>

    <form action="{{ route('goods-receipts.store', $purchaseOrder) }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header">{{ __('goods_receipts.Receipt Details') }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="receipt_date" class="form-label">{{ __('goods_receipts.Receipt Date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('receipt_date') is-invalid @enderror" id="receipt_date" name="receipt_date" value="{{ old('receipt_date', now()->format('Y-m-d')) }}" required>
                        @error('receipt_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">{{ __('goods_receipts.Notes') }}</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">{{ __('goods_receipts.Items to Receive') }}</div>
            <div class="card-body">
                @if($errors->has('items'))
                    <div class="alert alert-danger">
                        {{ $errors->first('items') }}
                    </div>
                @endif
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('goods_receipts.Product') }}</th>
                            <th class="text-end">{{ __('goods_receipts.Ordered') }}</th>
                            <th class="text-end">{{ __('goods_receipts.Remaining') }}</th>
                            <th style="width: 150px;" class="text-end">{{ __('goods_receipts.Quantity to Receive') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itemsToReceive as $index => $item)
                            <tr>
                                <td>
                                    {{ $item->item_name }}
                                    <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->purchase_order_item_id }}">
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ $item->quantity_remaining }}</td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity_received]" class="form-control text-end @error('items.'.$index.'.quantity_received') is-invalid @enderror" value="{{ old('items.'.$index.'.quantity_received', 0) }}" min="0" max="{{ $item->quantity_remaining }}" required>
                                    @error('items.'.$index.'.quantity_received')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">{{ __('goods_receipts.Receive Items') }}</button>
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection