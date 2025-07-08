@extends('layouts.app')

@section('title', __('journal_entries.Edit Journal Entry'))

@section('content')
<div class="container">
    <h1>{{ __('journal_entries.Edit Journal Entry') }} #{{ $journalEntry->journal_entry_id }}</h1>

    @if($journalEntry->referenceable_id)
        <div class="alert alert-warning">{{ __('journal_entries.Automatically generated warning') }}</div>
    @endif

    <form action="{{ route('journal-entries.update', $journalEntry->journal_entry_id) }}" method="POST">
        @method('PUT')
        @include('journal_entries._form')
    </form>
</div>
@endsection