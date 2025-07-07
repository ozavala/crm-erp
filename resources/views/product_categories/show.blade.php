@extends('layouts.app')

@section('title', __('Product Category Details'))

@section('content')
<div class="container">
    <h1>{{ __('Category') }}: {{ $productCategory->name }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            {{ __('Category ID') }}: {{ $productCategory->category_id }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('Name') }}:</strong> {{ $productCategory->name }}</p>
            <p><strong>{{ __('Parent Category') }}:</strong> {{ $productCategory->parentCategory->name ?? __('N/A') }}</p>
            <p><strong>{{ __('Description') }}:</strong></p>
            <p>{{ $productCategory->description ?: __('N/A') }}</p>
            <hr>
            <p><strong>{{ __('Created At') }}:</strong> {{ $productCategory->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('Updated At') }}:</strong> {{ $productCategory->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('product-categories.edit', $productCategory->category_id) }}" class="btn btn-warning">{{ __('Edit') }}</a>
                <form action="{{ route('product-categories.destroy', $productCategory->category_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
            <a href="{{ route('product-categories.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
        </div>
    </div>

    @if($productCategory->childCategories->isNotEmpty())
        <h5>{{ __('Sub-categories') }}</h5>
        <ul class="list-group mb-3">
            @foreach($productCategory->childCategories as $child)
                <li class="list-group-item"><a href="{{ route('product-categories.show', $child->category_id) }}">{{ $child->name }}</a></li>
            @endforeach
        </ul>
    @endif

    @if($productCategory->products->isNotEmpty())
        <h5>{{ __('Products in this Category') }}</h5>
        <ul class="list-group">
            @foreach($productCategory->products as $product)
                <li class="list-group-item"><a href="{{ route('products.show', $product->product_id) }}">{{ $product->name }}</a></li>
            @endforeach
        </ul>
    @else
        <p>{{ __('No products currently in this category.') }}</p>
    @endif

</div>
@endsection