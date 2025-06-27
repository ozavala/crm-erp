@extends('layouts.app')

@section('title', 'supplier Details - ' . $supplier->name)

@section('content')
<div class="container">
    <h1>supplier: {{ $supplier->full_name }}</h1>

    <div class="card">
        <div class="card-header">
            supplier ID: {{ $supplier->supplier_id }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $supplier->name }}</p>
                    <p><strong>Contact:</strong> {{ $supplier->contact_person}}</p>
                    <p><strong>Email:</strong> {{ $supplier->email ?: 'N/A' }}</p>
                    <p><strong>Phone Number:</strong> {{ $supplier->phone_number ?: 'N/A' }}</p>
                    
                </div>
                <div class="col-md-6">
                    <h5>Addresses</h5>
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
                        <p>No addresses on file.</p>
                    @endforelse
                    {{-- Old address fields (can be removed after migration) --}}
                    @if(empty($supplier->addresses->first()) && ($supplier->address_street || $supplier->address_city))
                        <p class="text-muted small"><em>Legacy Address: {{ $supplier->address_street }}, {{ $supplier->address_city }}, {{ $supplier->address_state }} {{ $supplier->address_postal_code }} {{ $supplier->address_country }}</em></p>
                    @endif
                </div>
            </div>

           
            <hr>

            <p><strong>Created By:</strong> {{ $supplier->createdBy ? $supplier->createdBy->full_name : 'N/A' }} ({{ $supplier->createdBy ? $supplier->createdBy->username : 'N/A' }})</p>
            <p><strong>Created At:</strong> {{ $supplier->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $supplier->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Contacts</h2>
            <a href="{{ route('contacts.create', ['supplier_id' => $supplier->supplier_id]) }}" class="btn btn-primary btn-sm">Add New Contact</a>
        </div>
        <div class="card-body">
            @if($supplier->contacts->isNotEmpty())
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->contacts as $contact)
                            <tr>
                                <td><a href="{{ route('contacts.show', $contact) }}">{{ $contact->first_name }} {{ $contact->last_name }}</a></td>
                                <td>{{ $contact->title }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>
                                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-secondary btn-sm">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No contacts found for this supplier.</p>
            @endif
        </div>
    </div>

    {{-- Notes Section --}}
    @include('partials._notes', ['model' => $supplier])

     {{-- Tasks Section --}}
    @include('partials._tasks', ['model' => $supplier])
</div>
@endsection