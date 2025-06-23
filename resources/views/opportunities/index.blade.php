@extends('layouts.app')

@section('title', 'All Opportunities')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>All Opportunities</h1>
        <a href="{{ route('opportunities.create') }}" class="btn btn-primary">Add New Opportunity</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Stage</th>
                        <th>Amount</th>
                        <th>Expected Close Date</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opportunities as $opportunity)
                        <tr>
                            <td><a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->name }}</a></td>
                            <td><a href="{{ route('customers.show', $opportunity->customer) }}">{{ $opportunity->customer->company_name ?: $opportunity->customer->full_name }}</a></td>
                            <td>
                                @if($opportunity->contact)
                                    <a href="{{ route('contacts.show', $opportunity->contact) }}">{{ $opportunity->contact->first_name }} {{ $opportunity->contact->last_name }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $opportunity->stage }}</td>
                            <td>{{ number_format($opportunity->amount, 2) }}</td>
                            <td>{{ $opportunity->expected_close_date?->format('Y-m-d') ?: 'N/A' }}</td>
                            <td>{{ $opportunity->assignedTo->full_name }}</td>
                            <td>
                                <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-secondary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No opportunities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $opportunities->links() }}
        </div>
    </div>
</div>
@endsection