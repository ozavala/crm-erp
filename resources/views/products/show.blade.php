@extends('layouts.app')

@section('title', __('Product/Service Details'))

@section('content')
<div class="container">
    <h1>{{ $product->name }} <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">{{ $product->is_active ? __('Active') : __('Inactive') }}</span></h1>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('ID') }}: {{ $product->product_id }} | {{ __('SKU') }}: {{ $product->sku ?: __('N/A') }}</span>
            <span class="badge bg-{{ $product->is_service ? 'info' : 'secondary' }} fs-6">{{ $product->type_name }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ __('Description') }}</h5>
                    <p>{{ $product->description ?: __('N/A') }}</p>
                </div>
                <div class="col-md-4">
                    <h5>{{ __('Details') }}</h5>
                    <p><strong>{{ __('Price') }}:</strong> ${{ number_format($product->price, 2) }}</p>
                    <p><strong>{{ __('Cost') }}:</strong> {{ $product->cost ? '$'.number_format($product->cost, 2) : __('N/A') }}</p>
                    @if(!$product->is_service)
                    <p><strong>{{ __('Category') }}:</strong> {{ $product->category->name ?? __('N/A') }}</p>
                    <p><strong>{{ __('Quantity on Hand') }}:</strong> {{ $product->quantity_on_hand }}</p>
                    @endif
                </div>
            </div>
            <hr>
            @if($product->features->isNotEmpty())
            <h5>{{ __('Product Features') }}</h5>
            <dl class="row mb-3">
                @foreach($product->features as $feature)
                <dt class="col-sm-3">{{ $feature->name }}</dt>
                <dd class="col-sm-9">{{ $feature->pivot->value }}</dd>
                @endforeach
            </dl>
            <hr>
            @endif
            
            @if(!$product->is_service && $product->warehouses->isNotEmpty())
            <h5>{{ __('Inventory Levels') }}</h5>
            <ul class="list-group mb-3">
                @foreach($product->warehouses as $warehouse)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $warehouse->name }}
                        <span class="badge bg-primary rounded-pill">{{ $warehouse->pivot->quantity }}</span>
                    </li>
                @endforeach
            </ul>
            <hr>
            @endif

            <h5>{{ __('Audit Information') }}</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('Created By') }}:</strong> {{ $product->createdBy ? $product->createdBy->full_name : __('N/A') }}</p>
                    <p><strong>{{ __('Created At') }}:</strong> {{ $product->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('Updated At') }}:</strong> {{ $product->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-warning">{{ __('Edit') }}</a>
                <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
        </div>
    </div>

    {{-- Placeholder for related items like inventory movements, inclusion in orders, etc. --}}
    {{-- <h3 class="mt-4">{{ __('Order History') }}</h3> --}}
    {{-- <p>{{ __('List of orders including this product/service.') }}</p> --}}
</div>
@endsection