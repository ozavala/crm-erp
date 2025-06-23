@extends('layouts.app')

@section('title', 'All Contacts')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>All Contacts</h1>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">Add New Contact</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Title</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td><a href="{{ route('contacts.show', $contact) }}">{{ $contact->first_name }} {{ $contact->last_name }}</a></td>
                            <td><a href="{{ route('customers.show', $contact->customer) }}">{{ $contact->customer->company_name ?: $contact->customer->full_name }}</a></td>
                            <td>{{ $contact->title }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                            <td>
                                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-secondary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No contacts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $contacts->links() }}
        </div>
    </div>
</div>
@endsection