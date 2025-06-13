@extends('layouts.app')

@section('title', 'Create New Opportunity')

@section('content')
<div class="container">
    <h1>Create New Opportunity</h1>

    <form action="{{ route('opportunities.store') }}" method="POST">
        @include('opportunities._form', ['opportunity' => new \App\Models\Opportunity()])
    </form>
</div>
@endsection