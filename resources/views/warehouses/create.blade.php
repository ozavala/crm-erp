@extends('layouts.app')

@section('title', 'Create Warehouse')

@section('content')
<div class="container">
    <h1>Create New Warehouse</h1>

    <form action="{{ route('warehouses.store') }}" method="POST">
        @include('warehouses._form', ['warehouse' => null])
    </form>
</div>
@endsection