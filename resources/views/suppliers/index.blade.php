@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('suppliers.title') }}</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">{{ __('suppliers.add_new_supplier') }}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="{{ __('suppliers.search_placeholder') }}" value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">{{ __('suppliers.search') }}</button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary ms-2">{{ __('suppliers.clear') }}</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('suppliers.id') }}</th>
                <th>{{ __('suppliers.name') }}</th>
                <th>{{ __('suppliers.contact_person') }}</th>
                <th>{{ __('suppliers.email') }}</th>
                <th>{{ __('suppliers.phone') }}</th>
                <th>{{ __('suppliers.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->supplier_id }}</td>
                    <td><a href="{{ route('suppliers.show', $supplier->supplier_id) }}">{{ $supplier->name }}</a></td>
                    <td>{{ $supplier->contact_person ?: 'N/A' }}</td>
                    <td>{{ $supplier->email ?: 'N/A' }}</td>
                    <td>{{ $supplier->phone_number ?: 'N/A' }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-warning btn-sm">{{ __('suppliers.edit') }}</a>
                        <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('suppliers.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('suppliers.no_suppliers_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $suppliers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection