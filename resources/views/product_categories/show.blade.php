@extends('layouts.app')

@section('title', 'Product Category Details')

@section('content')
<div class="container">
    <h1>Category: {{ $productCategory->name }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            Category ID: {{ $productCategory->category_id }}
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $productCategory->name }}</p>
            <p><strong>Parent Category:</strong> {{ $productCategory->parentCategory->name ?? 'N/A' }}</p>
            <p><strong>Description:</strong></p>
            <p>{{ $productCategory->description ?: 'N/A' }}</p>
            <hr>
            <p><strong>Created At:</strong> {{ $productCategory->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $productCategory->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('product-categories.edit', $productCategory->category_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('product-categories.destroy', $productCategory->category_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('product-categories.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    @if($productCategory->childCategories->isNotEmpty())
        <h5>Sub-categories</h5>
        <ul class="list-group mb-3">
            @foreach($productCategory->childCategories as $child)
                <li class="list-group-item"><a href="{{ route('product-categories.show', $child->category_id) }}">{{ $child->name }}</a></li>
            @endforeach
        </ul>
    @endif

    @if($productCategory->products->isNotEmpty())
        <h5>Products in this Category</h5>
        <ul class="list-group">
            @foreach($productCategory->products as $product)
                <li class="list-group-item"><a href="{{ route('products.show', $product->product_id) }}">{{ $product->name }}</a></li>
            @endforeach
        </ul>
    @else
        <p>No products currently in this category.</p>
    @endif

</div>
@endsection