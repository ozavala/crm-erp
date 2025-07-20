@extends('layouts.app')

@section('title', 'Create New Order')

@section('content')
<div class="container">
    <h1>Create New Order</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @include('orders._form', ['order' => new \App\Models\Order()])
    </form>
</div>
@endsection