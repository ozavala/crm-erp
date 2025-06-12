@extends('layouts.app')

@section('title', 'Product Categories')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Product Categories</h1>
        <a href="{{ route('product-categories.create') }}" class="btn btn-primary">Add New Category</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('product-categories.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search categories..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('product-categories.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Parent Category</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productCategories as $category)
                <tr>
                    <td>{{ $category->category_id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->parentCategory->name ?? 'N/A' }}</td>
                    <td>{{ Str::limit($category->description, 70) ?: 'N/A' }}</td>
                    <td>
                        <a href="{{ route('product-categories.show', $category->category_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('product-categories.edit', $category->category_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('product-categories.destroy', $category->category_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No product categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $productCategories->links() }}
    </div>
</div>
@endsection