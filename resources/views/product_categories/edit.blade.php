@extends('layouts.app')

@section('title', __('Edit Product Category'))

@section('content')
<div class="container">
    <h1>{{ __('Edit Product Category') }}: {{ $productCategory->name }}</h1>

    <form action="{{ route('product-categories.update', $productCategory->category_id) }}" method="POST">
        @method('PUT')
        @include('product_categories._form')
    </form>
</div>
@endsection