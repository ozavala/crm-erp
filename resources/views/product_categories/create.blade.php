@extends('layouts.app')

@section('title', __('Create Product Category'))

@section('content')
<div class="container">
    <h1>{{ __('Create New Product Category') }}</h1>

    <form action="{{ route('product-categories.store') }}" method="POST">
        @include('product_categories._form', ['productCategory' => new \App\Models\ProductCategory(), 'categories' => $categories])
    </form>
</div>
@endsection