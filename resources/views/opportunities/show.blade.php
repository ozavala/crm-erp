@extends('layouts.app')

@section('title', 'Opportunity Details')

@section('content')
<div class="container">
    <h1>{{ $opportunity->name }} <span class="badge bg-info fs-6">{{ $opportunity->stage }}</span></h1>

    <div class="card mb-4">
        <div class="card-header">
            Opportunity ID: {{ $opportunity->opportunity_id }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>Description</h5>
                    <p>{{ $opportunity->description ?: 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <h5>Details</h5>
                    <p><strong>Amount:</strong> ${{ number_format($opportunity->amount, 2) }}</p>
                    <p><strong>Probability:</strong> {{ $opportunity->probability ?? 'N/A' }}%</p>
                    <p><strong>Expected Close Date:</strong> {{ $opportunity->expected_close_date ? $opportunity->expected_close_date->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Related Lead:</strong>
                        @if($opportunity->lead)
                            <a href="{{ route('leads.show', $opportunity->lead_id) }}">{{ $opportunity->lead->title }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Related Customer:</strong>
                        @if($opportunity->customer)
                            <a href="{{ route('customers.show', $opportunity->customer_id) }}">{{ $opportunity->customer->full_name }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Assigned To:</strong> {{ $opportunity->assignedTo->full_name ?? 'N/A' }}</p>
                    <p><strong>Created By:</strong> {{ $opportunity->createdBy->full_name ?? 'N/A' }}</p>
                    <p><strong>Created At:</strong> {{ $opportunity->created_at->format('Y-m-d H:i:s') }}</p>
                    <p><strong>Updated At:</strong> {{ $opportunity->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('opportunities.edit', $opportunity->opportunity_id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('opportunities.destroy', $opportunity->opportunity_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <a href="{{ route('opportunities.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        {{-- Display existing quotations for this opportunity --}}
        <h3 class="mt-4">Quotations for this Opportunity</h3>
        @if($opportunity->quotations->isNotEmpty())
            <ul class="list-group">
                @foreach($opportunity->quotations as $quotation)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('quotations.show', $quotation->quotation_id) }}">{{ $quotation->subject }}</a>
                        <span>{{ $quotation->status }} - ${{ number_format($quotation->total_amount, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No quotations found for this opportunity yet.</p>
        @endif
            </div>

    {{-- Placeholder for Quotations related to this Opportunity --}}
    {{-- <h3 class="mt-4">Quotations</h3> --}}
    {{-- <p>List of quotations for this opportunity.</p> --}}
</div>
@endsection