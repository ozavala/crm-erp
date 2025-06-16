@extends('layouts.app')

@section('title', 'Edit Journal Entry')

@section('content')
<div class="container">
    <h1>Edit Journal Entry #{{ $journalEntry->journal_entry_id }}</h1>

    @if($journalEntry->referenceable_id)
        <div class="alert alert-warning">This journal entry was automatically generated and cannot be fully edited. Consider creating a reversing entry if adjustments are needed.</div>
    @endif

    <form action="{{ route('journal-entries.update', $journalEntry->journal_entry_id) }}" method="POST">
        @method('PUT')
        @include('journal_entries._form')
    </form>
</div>
@endsection