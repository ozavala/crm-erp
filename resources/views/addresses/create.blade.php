@extends('layouts.app')

@section('title', __('addresses.Create New Address'))

@section('content')
<div class="container">
    <h1>{{ __('addresses.Create New Address') }}</h1>
    <p class="text-muted">{{ __('addresses.Note: Addresses are typically created via their parent record (e.g., Customer, Supplier).') }}</p>
        <p class="text-muted">{{ __('addresses.Creating a standalone address requires specifying the parent type and ID.') }}</p>

    <form action="{{ route('addresses.store') }}" method="POST">
        @include('addresses._form', ['address' => new \App\Models\Address()])
    </form>
</div>
@endsection