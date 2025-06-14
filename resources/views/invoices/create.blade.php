@extends('layouts.app')

@section('title', 'Create New Invoice')

@section('content')
<div class="container">
    <h1>Create New Invoice</h1>

    <form action="{{ route('invoices.store') }}" method="POST">
        @include('invoices._form')
    </form>
</div>
@endsection