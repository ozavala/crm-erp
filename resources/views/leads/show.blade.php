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
                @if(!in_array($lead->status, ['Won', 'Lost']))
                    <form action="{{ route('leads.convertToCustomer', $lead->lead_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('leads.Are you sure you want to convert this lead? This will create a new Opportunity and mark the lead as Won.') }}');">
                        @csrf
                        <button type="submit" class="btn btn-success">Convert Lead</button>
                    </form>
                @endif
                @if($lead->status !== 'Lost') {{-- Allow deleting unless it's already "Lost" or "Won" (soft delete still works) --}}
                    <form action="{{ route('leads.destroy', $lead->lead_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('leads.Are you sure you want to delete this lead?') }}');">
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
    <div class="row mt-4">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('leads.Activities') }}</h4>
                </div>
                <div class="card-body">
                    @if($lead->activities->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($lead->activities as $activity)
                                <li class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $activity->type }}</h6>
                                        <small class="text-muted">{{ $activity->activity_date->format('M d, Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1">{{ nl2br(e($activity->description)) }}</p>
                                    <small class="text-muted">{{ __('leads.Logged by') }}: {{ $activity->user->full_name ?? __('leads.N/A') }}</small>
                                    {{-- Add edit/delete for activities later if needed --}}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>{{ __('leads.No activities recorded for this lead yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('leads.Log New Activity') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('leads.activities.store', $lead->lead_id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="activity_type" class="form-label">{{ __('leads.Activity Type') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="activity_type" name="type" required>
                                <option value="">{{ __('leads.Select Type') }}</option>
                                <option value="Call" {{ old('type') == 'Call' ? 'selected' : '' }}>{{ __('leads.Call') }}</option>
                                <option value="Email" {{ old('type') == 'Email' ? 'selected' : '' }}>{{ __('leads.Email') }}</option>
                                <option value="Meeting" {{ old('type') == 'Meeting' ? 'selected' : '' }}>{{ __('leads.Meeting') }}</option>
                                <option value="Note" {{ old('type') == 'Note' ? 'selected' : '' }}>{{ __('leads.Note') }}</option>
                                <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>{{ __('leads.Other') }}</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="activity_date" class="form-label">{{ __('leads.Activity Date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('activity_date') is-invalid @enderror" id="activity_date" name="activity_date" value="{{ old('activity_date', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('activity_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="activity_description" class="form-label">{{ __('leads.Description') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="activity_description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('leads.Add Activity') }}</button>
                    </form>
                </div>
            </div>
        </div>

    {{--<div class="card mt-4">
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
                <div class="list-group-item">{{ __('leads.No activities logged yet.') }}</div>
            @endforelse
        </div>--}}
    </div>
</div>
@endsection