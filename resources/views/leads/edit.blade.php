@extends('layouts.app')

@section('title', __('leads.Edit Lead'))

@section('content')
<div class="container">
    <h1>{{ __('leads.Edit Lead') }}: {{ $lead->title }}</h1>

    <form action="{{ route('leads.update', $lead->lead_id) }}" method="POST">
        @method('PUT')
        @include('leads._form')
    </form>
</div>
@endsection