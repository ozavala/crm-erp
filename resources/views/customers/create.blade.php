@extends('layouts.app')

@section('title', __('Create Customer'))

@section('content')
<div class="container">
    <h3>{{ __('Create New Customer') }}</h3>

    <form action="{{ route('customers.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        
        @include('customers._form', ['customer' => new \App\Models\Customer()])

        <button type="submit" class="btn btn-primary mt-3">{{ __('Create Customer') }}</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary mt-3">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection