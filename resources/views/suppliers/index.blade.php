@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Suppliers</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Add New Supplier</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by name, contact, email..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->supplier_id }}</td>
                    <td><a href="{{ route('suppliers.show', $supplier->supplier_id) }}">{{ $supplier->name }}</a></td>
                    <td>{{ $supplier->contact_person ?: 'N/A' }}</td>
                    <td>{{ $supplier->email ?: 'N/A' }}</td>
                    <td>{{ $supplier->phone_number ?: 'N/A' }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No suppliers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection