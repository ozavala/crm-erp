@extends('layouts.app')

@section('title', 'Edit Opportunity')

@section('content')
<div class="container">
    <h1>Edit Opportunity: {{ $opportunity->name }}</h1>

    <form action="{{ route('opportunities.update', $opportunity->opportunity_id) }}" method="POST">
        @method('PUT')
        @include('opportunities._form')
    </form>
</div>
@endsection