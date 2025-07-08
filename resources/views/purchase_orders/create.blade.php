@extends('layouts.app')

@section('title', 'Create New Purchase Order')

@section('content')
<div class="container">
    <h1>{{ __('purchase_orders.create_new_purchase_order') }}</h1>

    <form action="{{ route('purchase-orders.store') }}" method="POST">
        @include('purchase_orders._form')
    </form>
</div>
@endsection