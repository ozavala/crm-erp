@extends('layouts.app')

@section('title', 'Opportunity Details - ' . $opportunity->name)

@section('content')
<div class="container">
    <h1>Opportunity: {{ $opportunity->name }}</h1>

    <div class="card">
        <div class="card-header">
            Opportunity Details
        </div>
        <div class="card-body">
            <p><strong>Customer:</strong> <a href="{{ route('customers.show', $opportunity->customer) }}">{{ $opportunity->customer->company_name ?: $opportunity->customer->full_name }}</a></p>
            @if($opportunity->contact)
                <p><strong>Contact:</strong> <a href="{{ route('contacts.show', $opportunity->contact) }}">{{ $opportunity->contact->first_name }} {{ $opportunity->contact->last_name }}</a></p>
            @endif
            <p><strong>Stage:</strong> {{ $opportunity->stage }}</p>
            <p><strong>Amount:</strong> {{ number_format($opportunity->amount, 2) }}</p>
            <p><strong>Expected Close Date:</strong> {{ $opportunity->expected_close_date?->format('Y-m-d') ?: 'N/A' }}</p>
            <p><strong>Probability:</strong> {{ $opportunity->probability }}%</p>
            <p><strong>Assigned To:</strong> {{ $opportunity->assignedTo->full_name }}</p>
            <p><strong>Description:</strong> {{ $opportunity->description ?: 'N/A' }}</p>
            @if($opportunity->lead)
                <p><strong>Converted From Lead:</strong> <a href="{{ route('leads.show', $opportunity->lead) }}">{{ $opportunity->lead->title }}</a></p>
            @endif
            <hr>
            <p><strong>Created By:</strong> {{ $opportunity->createdBy->full_name }}</p>
            <p><strong>Created At:</strong> {{ $opportunity->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $opportunity->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('opportunities.destroy', $opportunity) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this opportunity?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('opportunities.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
    {{-- Notes Section --}}
    @include('partials._notes', ['model' => $opportunity])
</div>
@endsection