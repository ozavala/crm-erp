@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Customers</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">Add New Customer</a>
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
            <input type="text" name="search" class="form-control me-2" placeholder="Search by name, email, company..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->customer_id }}</td>
                    <td>{{ $customer->full_name }}</td>
                    <td>{{ $customer->email ?: 'N/A' }}</td>
                    <td>{{ $customer->phone_number ?: 'N/A' }}</td>
                    <td>{{ $customer->company_name ?: 'N/A' }}</td>
                    <td><span class="badge bg-{{ $customer->status == 'Active' ? 'success' : ($customer->status == 'Inactive' ? 'secondary' : ($customer->status == 'Lead' ? 'info' : 'warning')) }}">{{ $customer->status ?: 'N/A' }}</span></td>
                    <td>
                        <a href="{{ route('customers.show', $customer->customer_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $customers->links() }}
    </div>
</div>
@endsection