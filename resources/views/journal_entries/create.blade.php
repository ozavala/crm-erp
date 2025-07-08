@extends('layouts.app')

@section('title', __('journal_entries.Create New Journal Entry'))

@section('content')
<div class="container">
    <h1>{{ __('journal_entries.Create New Journal Entry') }}</h1>

    <form action="{{ route('journal-entries.store') }}" method="POST">
        @include('journal_entries._form')
    </form>
</div>
@endsection