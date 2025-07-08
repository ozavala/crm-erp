@extends('layouts.app')

@section('title', __('opportunities.Create New Opportunity'))

@section('content')
<div class="container">
    <h1>{{ __('opportunities.Create New Opportunity') }}</h1>

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