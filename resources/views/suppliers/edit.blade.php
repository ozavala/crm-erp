@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="container">
    <h3>Edit Supplier: {{ $supplier->name }}</h3>

    <form action="{{ route('suppliers.update', $supplier->supplier_id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('suppliers._form', ['supplier' => $supplier])
        <button type="submit" class="btn btn-primary mt-3">{{ __('Update Supplier') }}</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary mt-3">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection