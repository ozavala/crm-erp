@extends('layouts.app')

@section('title', 'Create Product Feature')

@section('content')
<div class="container">
    <h1>Create New Product Feature</h1>

    <form action="{{ route('product-features.store') }}" method="POST">
        @include('product_features._form', ['productFeature' => null])
    </form>
</div>
@endsection