@extends('layouts.app')

@section('title', 'Address Details')

@section('content')
<div class="container">
    <h1>Address #{{ $address->address_id }}</h1>

    <div class="card">
        <div class="card-header">
            Details
        </div>
        <div class="card-body">
            <p><strong>Address ID:</strong> {{ $address->address_id }}</p>
            <p><strong>Address Type:</strong> {{ $address->address_type ?: 'N/A' }}</p>
            <p><strong>Street Address Line 1:</strong> {{ $address->street_address_line_1 }}</p>
            <p><strong>Street Address Line 2:</strong> {{ $address->street_address_line_2 ?: 'N/A' }}</p>
            <p><strong>City:</strong> {{ $address->city }}</p>
            <p><strong>State/Province:</strong> {{ $address->state_province ?: 'N/A' }}</p>
            <p><strong>Postal Code:</strong> {{ $address->postal_code }}</p>
            <p><strong>Country Code:</strong> {{ $address->country_code }}</p>
            <p><strong>Is Primary:</strong> {!! $address->is_primary ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</p>

            @if($address->addressable)
                <hr>
                <h5>Associated With:</h5>
                <p>
                    <strong>Type:</strong> {{ class_basename($address->addressable_type) }}<br>
                    <strong>ID:</strong> {{ $address->addressable_id }}
                    {{-- You could try to link to the parent record if you have a consistent URL structure
                    @php $parentName = strtolower(Str::plural(class_basename($address->addressable_type))); @endphp
                    @if(Route::has($parentName . '.show'))
                        <a href="{{ route($parentName . '.show', $address->addressable_id) }}">(View Parent)</a>
                    @endif
                    --}}
                </p>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('addresses.edit', $address->address_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('addresses.destroy', $address->address_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('addresses.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection