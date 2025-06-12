@extends('layouts.app')

@section('title', 'Warehouse Details')

@section('content')
<div class="container">
    <h1>Warehouse: {{ $warehouse->name }} <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'danger' }}">{{ $warehouse->is_active ? 'Active' : 'Inactive' }}</span></h1>

    <div class="card">
        <div class="card-header">
            Warehouse ID: {{ $warehouse->warehouse_id }}
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $warehouse->name }}</p>
            <p><strong>Location:</strong> {{ $warehouse->location ?: 'N/A' }}</p>
            <p><strong>Address:</strong></p>
            <p>{{ nl2br(e($warehouse->address)) ?: 'N/A' }}</p>
            <hr>
            <p><strong>Created At:</strong> {{ $warehouse->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $warehouse->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('warehouses.edit', $warehouse->warehouse_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('warehouses.destroy', $warehouse->warehouse_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure? This might affect inventory records.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5>Inventory in this Warehouse</h5>
        </div>
        <div class="card-body">
            {{-- Placeholder for listing products and their quantities in this warehouse --}}
            <p>Inventory listing will go here once the product-warehouse link is established.</p>
        </div>
    </div>
</div>
@endsection