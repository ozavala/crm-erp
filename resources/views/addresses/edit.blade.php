@extends('layouts.app')

@section('title', __('addresses.Edit Address'))

@section('content')
<div class="container">
    <h1>{{ __('addresses.Edit Address') }} #{{ $address->address_id }}</h1>

    <form action="{{ route('addresses.update', $address->address_id) }}" method="POST">
        @method('PUT')
        @include('addresses._form')
    </form>
</div>
@endsection