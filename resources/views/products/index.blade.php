@extends('layouts.app')

@section('title', 'Products & Services')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Products & Services</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">Add New</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('products.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, SKU, description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type_filter" class="form-select">
                        <option value="">All Types</option>
                        <option value="product" {{ request('type_filter') == 'product' ? 'selected' : '' }}>Product</option>
                        <option value="service" {{ request('type_filter') == 'service' ? 'selected' : '' }}>Service</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status_filter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-2" title="Clear Filters"><i class="bi bi-x-lg"></i> Clear</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Type</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->product_id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku ?: 'N/A' }}</td>
                    <td><span class="badge bg-{{ $product->is_service ? 'info' : 'secondary' }}">{{ $product->type_name }}</span></td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->is_service ? 'N/A' : $product->quantity_on_hand }}</td>
                    <td><span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No products or services found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection