@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')
<div class="container">
    <h1>Create New Customer</h1>

    <form action="{{ route('customers.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        
        @include('customers._form', ['customer' => new \App\Models\Customer()])

        <button type="submit" class="btn btn-primary mt-3">Create Customer</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
@endsection