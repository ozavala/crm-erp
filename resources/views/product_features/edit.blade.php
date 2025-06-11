@extends('layouts.app')

@section('title', 'Edit Product Feature')

@section('content')
<div class="container">
    <h1>Edit Product Feature: {{ $productFeature->name }}</h1>

    <form action="{{ route('product-features.update', $productFeature->feature_id) }}" method="POST">
        @method('PUT')
        @include('product_features._form')
    </form>
</div>
@endsection