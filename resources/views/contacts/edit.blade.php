@extends('layouts.app')

@section('title', 'Edit Contact')

@section('content')
<div class="container">
    <h1>Edit Contact: {{ $contact->first_name }} {{ $contact->last_name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contacts.update', $contact) }}" method="POST">
                @csrf
                @method('PUT')
                @include('contacts._form')
            </form>
        </div>
    </div>
</div>
@endsection