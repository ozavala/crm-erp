@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')
<div class="container">
    <h1>Supplier: {{ $supplier->name }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            Supplier ID: {{ $supplier->supplier_id }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Contact Information</h5>
                    <p><strong>Name:</strong> {{ $supplier->name }}</p>
                    <p><strong>Contact Person:</strong> {{ $supplier->contact_person ?: 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $supplier->email ?: 'N/A' }}</p>
                    <p><strong>Phone Number:</strong> {{ $supplier->phone_number ?: 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Address</h5>
                    @forelse ($supplier->addresses as $address)
                        <div class="mb-2 p-2 border rounded {{ $address->is_primary ? 'border-primary' : '' }}">
                            <strong>{{ $address->address_type ?: 'Address' }} {{ $address->is_primary ? '(Primary)' : '' }}</strong><br>
                            {{ $address->street_address_line_1 }}<br>
                            @if($address->street_address_line_2)
                                {{ $address->street_address_line_2 }}<br>
                            @endif
                            {{ $address->city }}, {{ $address->state_province }} {{ $address->postal_code }}<br>
                            {{ $address->country_code }}
                        </div>
                    @empty
                        <p>No address on file.</p>
                    @endforelse
                </div>
            </div>

            @if($supplier->notes)
                <hr>
                <h5>Notes</h5>
                <p>{{ nl2br(e($supplier->notes)) }}</p>
            @endif
            <hr>
            <p><strong>Created At:</strong> {{ $supplier->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $supplier->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    {{-- Placeholder for related items like Purchase Orders or Products --}}
    {{-- <h3 class="mt-4">Related Purchase Orders</h3> --}}
</div>
@endsection