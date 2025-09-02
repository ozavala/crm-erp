@extends('layouts.app')

@section('title', __('journal_entries.Journal Entry Details'))

@section('content')
<div class="container">
    <h1>Journal Entry #{{ $journalEntry->journal_entry_id }}</h1>

    <div class="card">
        <div class="card-header">
            Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Entry ID:</strong> {{ $journalEntry->journal_entry_id }}</p>
                    <p><strong>Entry Date:</strong> {{ $journalEntry->entry_date->format('Y-m-d') }}</p>
                    <p><strong>Transaction Type:</strong> {{ $journalEntry->transaction_type ?: 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Description:</strong> {{ $journalEntry->description ?: 'N/A' }}</p>
                    <p><strong>Reference:</strong>
                        @if($journalEntry->referenceable)
                            {{ class_basename($journalEntry->referenceable_type) }} #{{ $journalEntry->referenceable_id }}
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Created By:</strong> {{ $journalEntry->createdBy->full_name ?? ($journalEntry->createdBy->username ?? 'N/A') }} on {{ $journalEntry->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <h5 class="mt-4">Entry Lines</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Description</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th>Entity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $line)
                    <tr>
                        <td>
                            {{ $line->account_name }}
                        </td>
                        <td>
                            {{ $line->description ?? '-' }}
                        </td>
                        <td class="text-end">${{ $line->debit_amount > 0 ? number_format($line->debit_amount, 2) : '-' }}</td>
                        <td class="text-end">${{ $line->credit_amount > 0 ? number_format($line->credit_amount, 2) : '-' }}</td>
                        <td>
                            @if($line->entity)
                                {{ class_basename($line->entity_type) }} #{{ $line->entity_id }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">Back to Journal Entries</a>
        </div>
    </div>
</div>
@endsection