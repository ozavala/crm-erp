@extends('layouts.app')

@section('title', 'Addresses')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>All Addresses</h1>
        <a href="{{ route('addresses.create') }}" class="btn btn-primary">Add New Address (Standalone)</a> --}}
        {{-- Standalone address creation is generally not recommended for polymorphic relations without context --}}
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('addresses.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search addresses..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('addresses.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Street Line 1</th>
                <th>City</th>
                <th>Postal Code</th>
                <th>Country</th>
                <th>Primary</th>
                <th>Belongs To</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($addresses as $address)
                <tr>
                    <td>{{ $address->address_id }}</td>
                    <td>{{ $address->address_type ?: 'N/A' }}</td>
                    <td><a href="{{ route('addresses.show', $address->address_id) }}">{{ $address->street_address_line_1 }}</a></td>
                    <td>{{ $address->city }}</td>
                    <td>{{ $address->postal_code }}</td>
                    <td>{{ $address->country_code }}</td>
                    <td>{!! $address->is_primary ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                    <td>
                        @if($address->addressable)
                            {{ class_basename($address->addressable_type) }} #{{ $address->addressable_id }}
                            {{-- You could add a link to the parent here if you have a consistent way to generate it --}}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('addresses.edit', $address->address_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        {{-- Delete button might be better handled on the parent's edit page --}}
                        <form action="{{ route('addresses.destroy', $address->address_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this address?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No addresses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $addresses->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection