@extends('layouts.app')

@section('title', __('Customers'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('Customers') }}</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">{{ __('Add New Customer') }}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('customers.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="{{ __('Search by name, email, company...') }}" value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">{{ __('Search') }}</button>
            @if(request('search'))
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary ms-2">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Full Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Company') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->customer_id }}</td>
                    <td>{{ $customer->full_name }}</td>
                    <td>{{ $customer->email ?: __('N/A') }}</td>
                    <td>{{ $customer->phone_number ?: __('N/A') }}</td>
                    <td>{{ $customer->company_name ?: __('N/A') }}</td>
                    <td><span class="badge bg-{{ $customer->status == 'Active' ? 'success' : ($customer->status == 'Inactive' ? 'secondary' : ($customer->status == 'Lead' ? 'info' : 'warning')) }}">{{ $customer->status ?: __('N/A') }}</span></td>
                    <td>
                        <a href="{{ route('customers.show', $customer->customer_id) }}" class="btn btn-info btn-sm">{{ __('View') }}</a>
                        <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning btn-sm">{{ __('Edit') }}</a>
                        <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('Are you sure you want to delete this customer?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">{{ __('No customers found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $customers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection