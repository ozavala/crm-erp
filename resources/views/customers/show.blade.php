@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container">
    <h1>Customer: {{ $customer->full_name }}</h1>

    <div class="card">
        <div class="card-header">
            Customer ID: {{ $customer->customer_id }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>First Name:</strong> {{ $customer->first_name }}</p>
                    <p><strong>Last Name:</strong> {{ $customer->last_name }}</p>
                    <p><strong>Email:</strong> {{ $customer->email ?: 'N/A' }}</p>
                    <p><strong>Phone Number:</strong> {{ $customer->phone_number ?: 'N/A' }}</p>
                    <p><strong>Company Name:</strong> {{ $customer->company_name ?: 'N/A' }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $customer->status == 'Active' ? 'success' : ($customer->status == 'Inactive' ? 'secondary' : ($customer->status == 'Lead' ? 'info' : 'warning')) }}">{{ $customer->status ?: 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <h5>Addresses</h5>
                    @forelse ($customer->addresses as $address)
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
                        <p>No addresses on file.</p>
                    @endforelse
                    {{-- Old address fields (can be removed after migration) --}}
                    @if(empty($customer->addresses->first()) && ($customer->address_street || $customer->address_city))
                        <p class="text-muted small"><em>Legacy Address: {{ $customer->address_street }}, {{ $customer->address_city }}, {{ $customer->address_state }} {{ $customer->address_postal_code }} {{ $customer->address_country }}</em></p>
                    @endif
                </div>
            </div>

            <hr>
            <h5>Notes</h5>
            <p>{{ $customer->notes ?: 'N/A' }}</p>
            <hr>

            <p><strong>Created By:</strong> {{ $customer->createdBy ? $customer->createdBy->full_name : 'N/A' }} ({{ $customer->createdBy ? $customer->createdBy->username : 'N/A' }})</p>
            <p><strong>Created At:</strong> {{ $customer->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $customer->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    {{-- Placeholder for related items like Orders, Invoices, etc. --}}
    {{-- <h3 class="mt-4">Related Orders</h3> --}}
    {{-- <p>Order listing will go here.</p> --}}
</div>
@endsection