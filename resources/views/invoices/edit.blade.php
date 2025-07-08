@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div class="container">
    <h1>Edit Invoice: {{ $invoice->invoice_number }}</h1>

    <form action="{{ route('invoices.update', $invoice->invoice_id) }}" method="POST">
        @method('PUT')
        @include('invoices._form')
    </form>
</div>
@endsection