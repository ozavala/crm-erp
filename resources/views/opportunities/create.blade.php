@extends('layouts.app')

@section('title', 'Create New Opportunity')

@section('content')
<div class="container">
    <h1>Create New Opportunity</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('opportunities.store') }}" method="POST">
                @csrf
                @include('opportunities._form')
            </form>
        </div>
    </div>
</div>
@endsection