@extends('layouts.app')

@section('title', __('opportunities.Opportunities'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Opportunities</h1>
        <div>
            <a href="{{ route('opportunities.kanban') }}" class="btn btn-outline-primary">Kanban View</a>
            <a href="{{ route('opportunities.create') }}" class="btn btn-primary">Add Opportunity</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Customer</th>
                        <th>Stage</th>
                        <th>Amount</th>
                        <th>Close Date</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opportunities as $opportunity)
                        <tr>
                            <td><a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->name }}</a></td>
                            <td>{{ $opportunity->customer ? ($opportunity->customer->company_name ?? $opportunity->customer->full_name) : 'N/A' }}</td>
                            <td>{{ $opportunity->stage }}</td>
                            <td>${{ number_format($opportunity->amount, 2) }}</td>
                            <td>{{ $opportunity->expected_close_date?->format('Y-m-d') }}</td>
                            <td>{{ $opportunity->assignedTo ? $opportunity->assignedTo->full_name : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-secondary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No opportunities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $opportunities->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection