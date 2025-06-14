@extends('layouts.app')

@section('title', 'Create New Supplier')

@section('content')
<div class="container">
    <h1>Create New Supplier</h1>

    <form action="{{ route('suppliers.store') }}" method="POST">
        @include('suppliers._form', ['supplier' => new \App\Models\Supplier()])
    </form>
</div>
@endsection