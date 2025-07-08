@extends('layouts.app')

@section('title', __('leads.Create New Lead'))

@section('content')
<div class="container">
    <h1>{{ __('leads.Create New Lead') }}</h1>

    <form action="{{ route('leads.store') }}" method="POST">
        @include('leads._form', ['lead' => null])
    </form>
</div>
@endsection