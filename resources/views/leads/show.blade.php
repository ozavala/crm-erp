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
                @if(!in_array($lead->status, ['Won', 'Lost'])) {{-- Show convert button only if not already Won or Lost --}}
                    <form action="{{ route('leads.convertToCustomer', $lead->lead_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to convert this lead to a customer? This will mark the lead as Won.');">
                        @csrf
                        <button type="submit" class="btn btn-success">Convert to Customer</button>
                    </form>
                @endif
                @if($lead->status !== 'Lost') {{-- Allow deleting unless it's already "Lost" or "Won" (soft delete still works) --}}
                    <form action="{{ route('leads.destroy', $lead->lead_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Lead</button>
                    </form>
                @endif
            </div>
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <!-- Activities Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Log Activity</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('leads.activities.store', $lead->lead_id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="activity_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="activity_type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="Note" {{ old('type') == 'Note' ? 'selected' : '' }}>Note</option>
                            <option value="Call" {{ old('type') == 'Call' ? 'selected' : '' }}>Call</option>
                            <option value="Email" {{ old('type') == 'Email' ? 'selected' : '' }}>Email</option>
                            <option value="Meeting" {{ old('type') == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                            <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="activity_date" class="form-label">Date</label>
                        <input type="datetime-local" name="activity_date" id="activity_date" class="form-control @error('activity_date') is-invalid @enderror" value="{{ old('activity_date', now()->format('Y-m-d\TH:i')) }}">
                        @error('activity_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="activity_description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" id="activity_description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Log Activity</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5>Activity History</h5>
        </div>
        <div class="list-group list-group-flush">
            @forelse ($lead->activities as $activity)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $activity->type }}
                            @if($activity->user)
                                <small class="text-muted">- by {{ $activity->user->full_name }}</small>
                            @endif
                        </h6>
                        <small>{{ $activity->activity_date->diffForHumans() }} ({{ $activity->activity_date->format('Y-m-d H:i') }})</small>
                    </div>
                    <p class="mb-1">{{ nl2br(e($activity->description)) }}</p>
                </div>
            @empty
                <div class="list-group-item">No activities logged yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection