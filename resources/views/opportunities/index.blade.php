@extends('layouts.app')

@section('title', __('opportunities.Opportunities'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('opportunities.Opportunities') }}</h1>
        <div>
            <a href="{{ route('opportunities.kanban') }}" class="btn btn-outline-primary">{{ __('opportunities.Kanban View') }}</a>
            <a href="{{ route('opportunities.create') }}" class="btn btn-primary">{{ __('opportunities.Create New Opportunity') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('opportunities.Name') }}</th>
                        <th>{{ __('opportunities.Customer') }}</th>
                        <th>{{ __('opportunities.Stage') }}</th>
                        <th>{{ __('opportunities.Amount') }}</th>
                        <th>{{ __('opportunities.Close Date') }}</th>
                        <th>{{ __('opportunities.Assigned To') }}</th>
                        <th>{{ __('opportunities.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opportunities as $opportunity)
                        <tr>
                            <td><a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->name }}</a></td>
                            <td>{{ $opportunity->customer->company_name ?? $opportunity->customer->full_name }}</td>
                            <td>{{ $opportunity->stage }}</td>
                            <td>${{ number_format($opportunity->amount, 2) }}</td>
                            <td>{{ $opportunity->expected_close_date?->format('Y-m-d') }}</td>
                            <td>{{ $opportunity->assignedTo?->full_name }}</td>
                            <td>
                                <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-secondary btn-sm">{{ __('opportunities.Edit') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('opportunities.No opportunities found.') }}</td>
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