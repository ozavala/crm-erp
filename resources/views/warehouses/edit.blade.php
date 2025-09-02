@extends('layouts.app')

@section('title', __('warehouses.edit'))

@section('content')
<div class="container">
    <h1>{{ __('warehouses.edit') }}: {{ $warehouse->name }}</h1>

    <form action="{{ route('warehouses.update', $warehouse->warehouse_id) }}" method="POST">
        @method('PUT')
        @include('warehouses._form')
    </form>
</div>
@endsection