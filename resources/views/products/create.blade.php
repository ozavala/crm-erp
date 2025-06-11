@extends('layouts.app')

@section('title', 'Create New Product/Service')

@section('content')
<div class="container">
    <h1>Create New Product/Service</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @include('products._form', ['product' => new \App\Models\Product()])
    </form>
</div>
@endsection