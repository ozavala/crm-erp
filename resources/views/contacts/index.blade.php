@extends('layouts.app')

@section('title', __('contacts.All Contacts'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('contacts.All Contacts') }}</h1>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">{{ __('contacts.Add New Contact') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('contacts.Name') }}</th>
                        <th>{{ __('contacts.Company') }}</th>
                        <th>{{ __('contacts.Title') }}</th>
                        <th>{{ __('contacts.Email') }}</th>
                        <th>{{ __('contacts.Phone') }}</th>
                        <th>{{ __('contacts.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td><a href="{{ route('contacts.show', $contact) }}">{{ $contact->first_name }} {{ $contact->last_name }}</a></td>
                            <td><x-polymorphic-link :model="$contact->contactable" /></td>
                            <td>{{ $contact->title }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                            <td>
                                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-secondary btn-sm">{{ __('contacts.Edit') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('contacts.No contacts found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $contacts->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection