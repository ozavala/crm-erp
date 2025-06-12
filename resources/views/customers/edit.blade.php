@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container">
    <h1>Edit Customer: {{ $customer->full_name }}</h1>

    <form action="{{ route('customers.update', $customer->customer_id) }}" method="POST">
        @method('PUT')
        {{-- This line includes the form partial --}}
        @include('customers._form', ['customer' => $customer, 'statuses' => $statuses])
    </form>
</div>
@endsection