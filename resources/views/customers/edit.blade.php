@extends('layouts.app')

@section('title', __('Edit Customer') . ': ' . $customer->full_name)

@section('content')
<div class="container">
    <h1>{{ __('Edit Customer') }}: {{ $customer->full_name }}</h1>

    <form action="{{ route('customers.update', $customer->customer_id) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        {{-- This line includes the form partial --}}
       @include('customers._form', ['customer' => $customer])

        {{-- Address fields can be added here or within the form partial --}}

        <button type="submit" class="btn btn-primary mt-3">{{ __('Update Customer') }}</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary mt-3">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection