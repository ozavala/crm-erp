@extends('layouts.app')

@section('title', __('leads.Leads'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('leads.Leads') }}</h1>
        <a href="{{ route('leads.create') }}" class="btn btn-primary">{{ __('leads.Add New Lead') }}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('leads.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('leads.Search by title, contact, customer...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status_filter" class="form-select">
                        <option value="">{{ __('leads.All Statuses') }}</option>
                        @foreach($filterStatuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="source_filter" class="form-select">
                        <option value="">{{ __('leads.All Sources') }}</option>
                        @foreach($filterSources as $key => $label)
                            <option value="{{ $key }}" {{ request('source_filter') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="assigned_to_filter" class="form-select">
                        <option value="">{{ __('leads.All Users') }}</option>
                        @foreach($crmUsers as $id => $name)
                            <option value="{{ $id }}" {{ request('assigned_to_filter') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">{{ __('leads.Filter') }}</button>
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary ms-2" title="{{ __('leads.Clear Filters') }}"><i class="bi bi-x-lg"></i> {{ __('leads.Clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('leads.ID') }}</th>
                <th>{{ __('leads.Title') }}</th>
                <th>{{ __('leads.Status') }}</th>
                <th>{{ __('leads.Value') }}</th>
                <th>{{ __('leads.Customer / Contact') }}</th>
                <th>{{ __('leads.Assigned To') }}</th>
                <th>{{ __('leads.Expected Close') }}</th>
                <th>{{ __('leads.Actions') }}</th>
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
                        <a href="{{ route('leads.show', $lead->lead_id) }}" class="btn btn-info btn-sm">{{ __('leads.View') }}</a>
                        <a href="{{ route('leads.edit', $lead->lead_id) }}" class="btn btn-warning btn-sm">{{ __('leads.Edit') }}</a>
                        <form action="{{ route('leads.destroy', $lead->lead_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('leads.Are you sure you want to delete this lead?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('leads.Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">{{ __('leads.No leads found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $leads->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection