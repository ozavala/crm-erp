@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('quotations.title') }}</h1>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary">{{ __('quotations.add_new_quotation') }}</a>
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
                    <input type="text" name="search" class="form-control" placeholder="{{ __('quotations.search_placeholder') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status_filter" class="form-select">
                        <option value="">{{ __('quotations.all_statuses') }}</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">{{ __('quotations.filter') }}</button>
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary ms-2" title="{{ __('quotations.clear_filters') }}"><i class="bi bi-x-lg"></i> {{ __('quotations.clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('quotations.id') }}</th>
                <th>{{ __('quotations.subject') }}</th>
                <th>{{ __('quotations.opportunity') }}</th>
                <th>{{ __('quotations.customer') }}</th>
                <th>{{ __('quotations.status') }}</th>
                <th>{{ __('quotations.total') }}</th>
                <th>{{ __('quotations.date') }}</th>
                <th>{{ __('quotations.actions') }}</th>
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
                        <a href="{{ route('quotations.edit', $quotation->quotation_id) }}" class="btn btn-warning btn-sm">{{ __('quotations.edit') }}</a>
                        <form action="{{ route('quotations.destroy', $quotation->quotation_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('quotations.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">{{ __('quotations.no_quotations_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $quotations->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection