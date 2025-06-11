@extends('layouts.app')

@section('title', 'Leads')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Leads</h1>
        <a href="{{ route('leads.create') }}" class="btn btn-primary">Add New Lead</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('leads.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by title, contact, customer..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Value</th>
                <th>Customer / Contact</th>
                <th>Assigned To</th>
                <th>Expected Close</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leads as $lead)
                <tr>
                    <td>{{ $lead->lead_id }}</td>
                    <td>{{ $lead->title }}</td>
                    <td><span class="badge bg-info">{{ $lead->status }}</span></td>
                    <td>{{ $lead->value ? number_format($lead->value, 2) : 'N/A' }}</td>
                    <td>{{ $lead->customer ? $lead->customer->full_name . ($lead->customer->company_name ? ' ('.$lead->customer->company_name.')' : '') : ($lead->contact_name ?: 'N/A') }}</td>
                    <td>{{ $lead->assignedTo ? $lead->assignedTo->full_name : 'N/A' }}</td>
                    <td>{{ $lead->expected_close_date ? $lead->expected_close_date->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('leads.show', $lead->lead_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('leads.edit', $lead->lead_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('leads.destroy', $lead->lead_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No leads found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $leads->links() }}
    </div>
</div>
@endsection