@extends('layouts.app')

@section('title', __('opportunities.Edit Opportunity'))

@section('content')
<div class="container">
    <h1>{{ __('opportunities.Edit Opportunity') }}: {{ $opportunity->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('opportunities.update', $opportunity) }}" method="POST">
                @csrf
                @method('PUT')
                @include('opportunities._form')
            </form>
        </div>
    </div>
</div>
@endsection