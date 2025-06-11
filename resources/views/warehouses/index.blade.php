@extends('layouts.app')

@section('title', 'Warehouses')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Warehouses</h1>
        <a href="{{ route('warehouses.create') }}" class="btn btn-primary">Add New Warehouse</a>
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
        <form action="{{ route('warehouses.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by name or location..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($warehouses as $warehouse)
                <tr>
                    <td>{{ $warehouse->warehouse_id }}</td>
                    <td>{{ $warehouse->name }}</td>
                    <td>{{ $warehouse->location ?: 'N/A' }}</td>
                    <td><span class="badge bg-{{ $warehouse->is_active ? 'success' : 'danger' }}">{{ $warehouse->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('warehouses.show', $warehouse->warehouse_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('warehouses.edit', $warehouse->warehouse_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('warehouses.destroy', $warehouse->warehouse_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure? This might affect inventory records.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No warehouses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $warehouses->links() }}
    </div>
</div>
@endsection