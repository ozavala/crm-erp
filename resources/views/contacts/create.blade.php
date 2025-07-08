@extends('layouts.app')

@section('title', __('contacts.Create New Contact'))

@section('content')
<div class="container">
    <h1>{{ __('contacts.Create New Contact') }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contacts.store') }}" method="POST">
                @csrf
                @include('contacts._form')
            </form>
        </div>
    </div>
</div>
@endsection