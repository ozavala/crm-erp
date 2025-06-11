@extends('layouts.app')

@section('title', 'Product Features')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Product Features</h1>
        <a href="{{ route('product-features.create') }}" class="btn btn-primary">Add New Feature</a>
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
        <form action="{{ route('product-features.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search features..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('product-features.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productFeatures as $feature)
                <tr>
                    <td>{{ $feature->feature_id }}</td>
                    <td>{{ $feature->name }}</td>
                    <td>{{ Str::limit($feature->description, 70) ?: 'N/A' }}</td>
                    <td>
                        <a href="{{ route('product-features.show', $feature->feature_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('product-features.edit', $feature->feature_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('product-features.destroy', $feature->feature_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this feature?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No product features found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $productFeatures->links() }}
    </div>
</div>
@endsection