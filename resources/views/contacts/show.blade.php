@extends('layouts.app')

@section('title', 'Contact Details')

@section('content')
<div class="container">
    <h1>Contact: {{ $contact->first_name }} {{ $contact->last_name }}</h1>

    <div class="card">
        <div class="card-header">Contact Details</div>
        <div class="card-body">
            <p>
                <strong>Company:</strong>
                <x-polymorphic-link :model="$contact->contactable" />
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
                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="display:inline-block;" onsubmit="return confirm(__('contacts.Are you sure you want to delete this contact?'));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <x-back-to-parent-link :parent="$contact->contactable" fallback-route="contacts.index" fallback-text="Contacts" class="btn btn-secondary" />
        </div>
    </div>
</div>
@endsection