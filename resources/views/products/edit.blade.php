@extends('layouts.app')

@section('title', __('Edit Product/Service'))

@section('content')
<div class="container">
    <h1>{{ __('Edit Product/Service') }}: {{ $product->name }}</h1>

    <form action="{{ route('products.update', $product->product_id) }}" method="POST">
        @method('PUT')
        @include('products._form')
    </form>
</div>
@endsection