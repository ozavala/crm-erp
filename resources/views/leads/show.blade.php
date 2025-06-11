@extends('layouts.app')

@section('title', 'Lead Details')

@section('content')
<div class="container">
    <h1>Lead: {{ $lead->title }}</h1>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Lead ID: {{ $lead->lead_id }}
            <span class="badge bg-info fs-6">{{ $lead->status }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                    <p><strong>Description:</strong></p>
                    <p>{{ $lead->description ?: 'N/A' }}</p>
                    <hr>
                    <p><strong>Potential Value:</strong> ${{ $lead->value ? number_format($lead->value, 2) : 'N/A' }}</p>
                    <p><strong>Source:</strong> {{ $lead->source ?: 'N/A' }}</p>
                    <p><strong>Expected Close Date:</strong> {{ $lead->expected_close_date ? $lead->expected_close_date->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div class="col-md-5">
                    <h5>Contact & Assignment</h5>
                    @if($lead->customer)
                        <p><strong>Associated Customer:</strong> <a href="{{ route('customers.show', $lead->customer_id) }}">{{ $lead->customer->full_name }}</a> {{ $lead->customer->company_name ? '('.$lead->customer->company_name.')' : '' }}</p>
                    @else
                        <p><strong>Contact Name:</strong> {{ $lead->contact_name ?: 'N/A' }}</p>
                        <p><strong>Contact Email:</strong> {{ $lead->contact_email ?: 'N/A' }}</p>
                        <p><strong>Contact Phone:</strong> {{ $lead->contact_phone ?: 'N/A' }}</p>
                    @endif
                    <hr>
                    <p><strong>Assigned To:</strong> {{ $lead->assignedTo ? $lead->assignedTo->full_name : 'Unassigned' }}</p>
                    <p><strong>Created By:</strong> {{ $lead->createdBy ? $lead->createdBy->full_name : 'N/A' }}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Created At:</strong> {{ $lead->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Updated At:</strong> {{ $lead->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('leads.edit', $lead->lead_id) }}" class="btn btn-warning">Edit Lead</a>
                <form action="{{ route('leads.destroy', $lead->lead_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Lead</button>
                </form>
            </div>
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    {{-- Placeholder for related activities, notes, tasks etc. --}}
    {{-- <h3 class="mt-4">Activities</h3> --}}
    {{-- <p>Activity stream or task list will go here.</p> --}}
</div>
@endsection