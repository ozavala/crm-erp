@extends('layouts.app')

@section('title', __('opportunities.Opportunity Details') . ' - ' . $opportunity->name)

@section('content')
<div class="container">
    <h1>{{ __('opportunities.Opportunity') }}: {{ $opportunity->name }}</h1>

    <div class="card">
        <div class="card-header">
            {{ __('opportunities.Opportunity Details') }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('opportunities.Customer') }}:</strong> <a href="{{ route('customers.show', $opportunity->customer) }}">{{ $opportunity->customer->company_name ?: $opportunity->customer->full_name }}</a></p>
            @if($opportunity->contact)
                <p><strong>{{ __('opportunities.Contact') }}:</strong> <a href="{{ route('contacts.show', $opportunity->contact) }}">{{ $opportunity->contact->first_name }} {{ $opportunity->contact->last_name }}</a></p>
            @endif
            <p><strong>{{ __('opportunities.Stage') }}:</strong> {{ $opportunity->stage }}</p>
            <p><strong>{{ __('opportunities.Amount') }}:</strong> {{ number_format($opportunity->amount, 2) }}</p>
            <p><strong>{{ __('opportunities.Expected Close Date') }}:</strong> {{ $opportunity->expected_close_date?->format('Y-m-d') ?: __('opportunities.N/A') }}</p>
            <p><strong>{{ __('opportunities.Probability') }}:</strong> {{ $opportunity->probability }}%</p>
            <p><strong>{{ __('opportunities.Assigned To') }}:</strong> {{ $opportunity->assignedTo->full_name }}</p>
            <p><strong>{{ __('opportunities.Description') }}:</strong> {{ $opportunity->description ?: __('opportunities.N/A') }}</p>
            @if($opportunity->lead)
                <p><strong>{{ __('opportunities.Converted From Lead') }}:</strong> <a href="{{ route('leads.show', $opportunity->lead) }}">{{ $opportunity->lead->title }}</a></p>
            @endif
            <hr>
            <p><strong>{{ __('opportunities.Created By') }}:</strong> {{ $opportunity->createdBy->full_name }}</p>
            <p><strong>{{ __('opportunities.Created At') }}:</strong> {{ $opportunity->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('opportunities.Updated At') }}:</strong> {{ $opportunity->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-warning">{{ __('opportunities.Edit') }}</a>
                <a href="{{ route('quotations.create', ['opportunity_id' => $opportunity->opportunity_id]) }}" class="btn btn-primary">{{ __('opportunities.Create Quotation') }}</a>
                <form action="{{ route('opportunities.destroy', $opportunity) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this opportunity?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('opportunities.Delete') }}</button>
                </form>
            </div>
            <a href="{{ route('opportunities.index') }}" class="btn btn-secondary">{{ __('opportunities.Back to List') }}</a>
        </div>
    </div>
    {{-- Notes Section --}}
    @include('partials._notes', ['model' => $opportunity])
</div>
@endsection