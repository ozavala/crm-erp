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
            @php
                $companyDisplay = 'N/A'; // Default display value
                // Only attempt to create a link if the contactable relationship is valid and has an ID.
                if ($contact->contactable_id && $contact->contactable) {
                    if ($contact->contactable_type === \App\Models\Customer::class) {
                        $url = route('customers.show', $contact->contactable_id);
                        $text = $contact->contactable->company_name ?: $contact->contactable->full_name;
                        $companyDisplay = "<a href=\"{$url}\">{$text}</a>";
                    } elseif ($contact->contactable_type === \App\Models\Supplier::class) {
                        $url = route('suppliers.show', $contact->contactable_id);
                        $text = $contact->contactable->name;
                        $companyDisplay = "<a href=\"{$url}\">{$text}</a>";
                    }
                }
            @endphp
            <p>
                <strong>Company:</strong>
                {!! $companyDisplay !!}
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
            @php
                $backUrl = route('contacts.index'); // Default fallback URL
                $backText = 'Contacts'; // Default fallback text

                // Check if a valid contactable parent exists
                if ($contact->contactable_id && $contact->contactable_type) {
                    if ($contact->contactable_type === \App\Models\Customer::class) {
                        $backUrl = route('customers.show', $contact->contactable_id);
                        $backText = 'Customer';
                    } elseif ($contact->contactable_type === \App\Models\Supplier::class) {
                        $backUrl = route('suppliers.show', $contact->contactable_id);
                        $backText = 'Supplier';
                    }
                }
            @endphp
            <a href="{{ $backUrl }}" class="btn btn-secondary">Back to {{ $backText }}</a>
        </div>
    </div>
</div>
@endsection