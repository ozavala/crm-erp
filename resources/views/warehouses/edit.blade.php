@extends('layouts.app')

@section('title', 'Edit Warehouse')

@section('content')
<div class="container">
    <h1>Edit Warehouse: {{ $warehouse->name }}</h1>

    <form action="{{ route('warehouses.update', $warehouse->warehouse_id) }}" method="POST">
        @method('PUT')
        @include('warehouses._form')
    </form>
</div>
@endsection