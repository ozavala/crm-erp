@extends('layouts.app')

@section('title', 'Create New Customer')

@section('content')
<div class="container">
    <h1>Create New Customer</h1>

    <form action="{{ route('customers.store') }}" method="POST">
        {{-- This line includes the form partial --}}
        @include('customers._form', ['customer' => new \App\Models\Customer(), 'statuses' => $statuses])
    </form>
</div>
@endsection