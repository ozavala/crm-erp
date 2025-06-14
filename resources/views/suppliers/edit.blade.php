@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="container">
    <h1>Edit Supplier: {{ $supplier->name }}</h1>

    <form action="{{ route('suppliers.update', $supplier->supplier_id) }}" method="POST">
        @method('PUT')
        @include('suppliers._form')
    </form>
</div>
@endsection