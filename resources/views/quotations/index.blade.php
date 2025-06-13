@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Quotations</h1>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary">Add New Quotation</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('quotations.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by subject, opportunity, customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status_filter" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary ms-2" title="Clear Filters"><i class="bi bi-x-lg"></i> Clear</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Opportunity</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($quotations as $quotation)
                <tr>
                    <td>{{ $quotation->quotation_id }}</td>
                    <td><a href="{{ route('quotations.show', $quotation->quotation_id) }}">{{ $quotation->subject }}</a></td>
                    <td>{{ $quotation->opportunity->name ?? 'N/A' }}</td>
                    <td>{{ $quotation->opportunity->customer->full_name ?? 'N/A' }}</td>
                    <td><span class="badge bg-secondary">{{ $quotation->status }}</span></td>
                    <td>${{ number_format($quotation->total_amount, 2) }}</td>
                    <td>{{ $quotation->quotation_date->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('quotations.edit', $quotation->quotation_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('quotations.destroy', $quotation->quotation_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No quotations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $quotations->links() }}
    </div>
</div>
@endsection