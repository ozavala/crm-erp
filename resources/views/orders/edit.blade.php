@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="container">
    <h1>Edit Order: {{ $order->order_number ?: ('Order #'.$order->order_id) }}</h1>

    <form action="{{ route('orders.update', $order->order_id) }}" method="POST">
        @method('PUT')
        @include('orders._form')
    </form>
</div>
@endsection