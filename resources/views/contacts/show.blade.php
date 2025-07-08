@extends('layouts.app')

@section('title', __('contacts.Contact Details'))

@section('content')
<div class="container">
    <h1>{{ __('contacts.Contact') }}: {{ $contact->first_name }} {{ $contact->last_name }}</h1>

    <div class="card">
        <div class="card-header">
            {{ __('contacts.Contact Details') }}
        </div>
        <div class="card-body">
            <p>
                <strong>{{ __('contacts.Company:') }}</strong>
                <x-polymorphic-link :model="$contact->contactable" />
            </p>
            <p><strong>{{ __('contacts.Title:') }}</strong> {{ $contact->title ?: __('N/A') }}</p>
            <p><strong>{{ __('contacts.Email:') }}</strong> {{ $contact->email ?: __('N/A') }}</p>
            <p><strong>{{ __('contacts.Phone:') }}</strong> {{ $contact->phone ?: __('N/A') }}</p>
            <hr>
            <p><strong>{{ __('contacts.Created At:') }}</strong> {{ $contact->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('contacts.Updated At:') }}</strong> {{ $contact->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">{{ __('contacts.Edit') }}</a>
                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="display:inline-block;" onsubmit="return confirm(__('contacts.Are you sure you want to delete this contact?'));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('contacts.Delete') }}</button>
                </form>
            </div>
            <x-back-to-parent-link :parent="$contact->contactable" fallback-route="contacts.index" fallback-text="{{ __('contacts.Contacts') }}" class="btn btn-secondary" />
        </div>
    </div>
</div>
@endsection