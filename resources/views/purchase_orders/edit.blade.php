@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="container">
    <h1>Edit Purchase Order: {{ $purchaseOrder->purchase_order_number }}</h1>

    <form action="{{ route('purchase-orders.update', $purchaseOrder->purchase_order_id) }}" method="POST">
        @method('PUT')
        @include('purchase_orders._form')
    </form>
</div>
@endsection