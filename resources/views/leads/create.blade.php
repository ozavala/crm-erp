@extends('layouts.app')

@section('title', 'Create Lead')

@section('content')
<div class="container">
    <h1>Create Lead</h1>

    <form action="{{ route('leads.store') }}" method="POST">
        @include('leads._form', ['lead' => null])
    </form>
</div>
@endsection