@extends('layouts.app')

@section('title', 'Create New Quotation')

@section('content')
<div class="container">
    <h1>Create New Quotation</h1>

    <form action="{{ route('quotations.store') }}" method="POST">
        @include('quotations._form', ['quotation' => new \App\Models\Quotation()])
    </form>
</div>
@endsection