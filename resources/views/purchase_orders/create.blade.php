@extends('layouts.app')

@section('title', 'Create New Purchase Order')

@section('content')
<div class="container">
    <h1>Create New Purchase Order</h1>

    <form action="{{ route('purchase-orders.store') }}" method="POST">
        @include('purchase_orders._form', ['purchaseOrder' => new \App\Models\PurchaseOrder()])
    </form>
</div>
@endsection