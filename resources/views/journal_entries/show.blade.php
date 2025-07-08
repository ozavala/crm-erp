@extends('layouts.app')

@section('title', __('journal_entries.Journal Entry Details'))

@section('content')
<div class="container">
    <h1>{{ __('journal_entries.Journal Entry') }} #{{ $journalEntry->journal_entry_id }}</h1>

    <div class="card">
        <div class="card-header">
            {{ __('journal_entries.Details') }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('journal_entries.Entry ID') }}:</strong> {{ $journalEntry->journal_entry_id }}</p>
                    <p><strong>{{ __('journal_entries.Entry Date') }}:</strong> {{ $journalEntry->entry_date->format('Y-m-d') }}</p>
                    <p><strong>{{ __('journal_entries.Transaction Type') }}:</strong> {{ $journalEntry->transaction_type ?: __('journal_entries.N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('journal_entries.Description') }}:</strong> {{ $journalEntry->description ?: __('journal_entries.N/A') }}</p>
                    <p><strong>{{ __('journal_entries.Reference') }}:</strong>
                        @if($journalEntry->referenceable)
                            {{ class_basename($journalEntry->referenceable_type) }} #{{ $journalEntry->referenceable_id }}
                            {{-- You could add a link here if you have consistent show routes for referenceable models --}}
                        @else
                            {{ __('journal_entries.N/A') }}
                        @endif
                    </p>
                    <p><strong>{{ __('journal_entries.Created By') }}:</strong> {{ $journalEntry->createdBy->full_name ?? ($journalEntry->createdBy->username ?? __('journal_entries.N/A')) }} {{ __('journal_entries.on') }} {{ $journalEntry->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <h5 class="mt-4">{{ __('journal_entries.Entry Lines') }}</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('journal_entries.Account Name') }}</th>
                        <th class="text-end">{{ __('journal_entries.Debit') }}</th>
                        <th class="text-end">{{ __('journal_entries.Credit') }}</th>
                        <th>{{ __('journal_entries.Entity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $line)
                    <tr>
                        <td>{{ $line->account_name }}</td>
                        <td class="text-end">${{ $line->debit_amount > 0 ? number_format($line->debit_amount, 2) : '-' }}</td>
                        <td class="text-end">${{ $line->credit_amount > 0 ? number_format($line->credit_amount, 2) : '-' }}</td>
                        <td>
                            @if($line->entity)
                                {{ class_basename($line->entity_type) }} #{{ $line->entity_id }}
                                {{-- Add link to entity if possible --}}
                            @else
                                {{ __('journal_entries.N/A') }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">{{ __('journal_entries.Back to Journal Entries') }}</a>
            {{-- Add Edit/Delete for manual journal entries later if needed --}}
        </div>
    </div>
</div>
@endsection