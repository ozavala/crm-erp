@extends('layouts.app')

@section('title', 'User Role Details')

@section('content')
<div class="container">
    <h1>User Role: {{ $userRole->name }}</h1>

    <div class="card mb-3">
        <div class="card-header">
            Role Details
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $userRole->role_id }}</p>
            <p><strong>Name:</strong> {{ $userRole->name }}</p>
            <p><strong>Description:</strong> {{ $userRole->description ?: 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $userRole->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $userRole->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('user-roles.edit', $userRole->role_id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('user-roles.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Assigned Users ({{ $userRole->users->count() }})
        </div>
        <ul class="list-group list-group-flush">
            @forelse($userRole->users as $user)
                <li class="list-group-item">{{ $user->full_name }} ({{ $user->username }})</li>
            @empty
                <li class="list-group-item">No users assigned to this role.</li>
            @endforelse
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            Assigned Permissions ({{ $userRole->permissions->count() }})
        </div>
        <ul class="list-group list-group-flush">
            @forelse($userRole->permissions as $permission)
                <li class="list-group-item">{{ $permission->name }} <small class="text-muted">({{ $permission->description }})</small></li>
            @empty
                <li class="list-group-item">No permissions assigned to this role.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection