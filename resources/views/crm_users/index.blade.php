@extends('layouts.app')

@section('title', 'CRM Users')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>CRM Users</h1>
        <a href="{{ route('crm-users.create') }}" class="btn btn-primary">Add New User</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($crmUsers as $user)
                <tr>
                    <td>{{ $user->user_id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('crm-users.show', $user->user_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('crm-users.edit', $user->user_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('crm-users.destroy', $user->user_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No CRM users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $crmUsers->links() }}
    </div>
</div>
@endsection