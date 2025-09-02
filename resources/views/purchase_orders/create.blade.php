@extends('layouts.app')

@section('title', 'Create New Purchase Order')

@section('content')
<div class="container">
    <h1>Create Purchase Order</h1>

    <form action="{{ route('purchase-orders.store') }}" method="POST">
        @include('purchase_orders._form')
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection