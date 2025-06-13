@extends('layouts.app')

@section('title', 'Opportunities')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Opportunities</h1>
        <a href="{{ route('opportunities.create') }}" class="btn btn-primary">Add New Opportunity</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('opportunities.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, customer, lead..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="stage_filter" class="form-select">
                        <option value="">All Stages</option>
                        @foreach($stages as $key => $value)
                            <option value="{{ $key }}" {{ request('stage_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('opportunities.index') }}" class="btn btn-outline-secondary ms-2" title="Clear Filters"><i class="bi bi-x-lg"></i> Clear</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Customer/Lead</th>
                <th>Stage</th>
                <th>Amount</th>
                <th>Assigned To</th>
                <th>Close Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($opportunities as $opportunity)
                <tr>
                    <td>{{ $opportunity->opportunity_id }}</td>
                    <td><a href="{{ route('opportunities.show', $opportunity->opportunity_id) }}">{{ $opportunity->name }}</a></td>
                    <td>
                        @if($opportunity->customer) {{ $opportunity->customer->full_name }} (Customer)
                        @elseif($opportunity->lead) {{ $opportunity->lead->title }} (Lead)
                        @else N/A @endif
                    </td>
                    <td><span class="badge bg-info">{{ $opportunity->stage }}</span></td>
                    <td>${{ number_format($opportunity->amount, 2) }}</td>
                    <td>{{ $opportunity->assignedTo->full_name ?? 'N/A' }}</td>
                    <td>{{ $opportunity->expected_close_date ? $opportunity->expected_close_date->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('opportunities.edit', $opportunity->opportunity_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('opportunities.destroy', $opportunity->opportunity_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No opportunities found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $opportunities->links() }}
    </div>
</div>
@endsection