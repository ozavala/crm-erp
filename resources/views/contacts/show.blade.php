@extends('layouts.app')

@section('title', 'Contact Details')

@section('content')
<div class="container">
    <h1>Contact: {{ $contact->first_name }} {{ $contact->last_name }}</h1>

    <div class="card">
        <div class="card-header">
            Contact Details
        </div>
        <div class="card-body">
            <p><strong>Company:</strong>
                @if ($contact->contactable_id && $contact->contactable) {{-- Ensure ID and relation are present --}}
                    @if ($contact->contactable_type === \App\Models\Customer::class)
                        <a href="{{ route('customers.show', $contact->contactable_id) }}">{{ $contact->contactable->company_name ?: $contact->contactable->full_name }}</a>
                    @elseif ($contact->contactable_type === \App\Models\Supplier::class)
                        <a href="{{ route('suppliers.show', $contact->contactable_id) }}">{{ $contact->contactable->name }}</a>
                    @endif
            </p>
            <p><strong>Title:</strong> {{ $contact->title ?: 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $contact->email ?: 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $contact->phone ?: 'N/A' }}</p>
            <hr>
            <p><strong>Created At:</strong> {{ $contact->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $contact->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
           <a href="{{ $contact->contactable_id && $contact->contactable_type === \App\Models\Customer::class
                ? route('customers.show', $contact->contactable_id) // Only attempt if contactable_id is not null
                : ($contact->contactable_id && $contact->contactable_type === \App\Models\Supplier::class
                    ? route('suppliers.show', $contact->contactable_id) // Only attempt if contactable_id is not null
                    : route('contacts.index')) }}" class="btn btn-secondary">
                Back to {{ $contact->contactable_type ? class_basename($contact->contactable_type) : 'Contacts' }}
            </a>
        </div>
    </div>
</div>
@endsection