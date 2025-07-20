@extends('layouts.app')

@section('title', 'Edit Quotation')

@section('content')
<div class="container">
    <h1>{{ __('quotations.edit_quotation') }}: {{ $quotation->subject }}</h1>

    <form action="{{ route('quotations.update', $quotation->quotation_id) }}" method="POST">
        @method('PUT')
        @include('quotations._form')
    </form>
</div>
@endsection