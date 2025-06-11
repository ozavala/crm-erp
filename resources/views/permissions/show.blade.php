@extends('layouts.app')

@section('title', 'Permission Details')

@section('content')
<div class="container">
    <h1>Permission: {{ $permission->name }}</h1>

    <div class="card mb-3">
        <div class="card-header">
            Permission Details
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $permission->permission_id }}</p>
            <p><strong>Name:</strong> {{ $permission->name }}</p>
            <p><strong>Description:</strong> {{ $permission->description ?: 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $permission->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Updated At:</strong> {{ $permission->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('permissions.edit', $permission->permission_id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Assigned to Roles ({{ $permission->roles->count() }})
        </div>
        <ul class="list-group list-group-flush">
            @forelse($permission->roles as $role)
                <li class="list-group-item">{{ $role->name }}</li>
            @empty
                <li class="list-group-item">This permission is not assigned to any roles yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection