@extends('layouts.app')

@section('title', 'Permission Details')

@section('content')
<div class="container">
    <h1>{{ __('permissions.permission') }}: {{ $permission->name }}</h1>

    <div class="card mb-3">
        <div class="card-header">
            {{ __('permissions.permission_details') }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('permissions.id') }}:</strong> {{ $permission->permission_id }}</p>
            <p><strong>{{ __('permissions.name') }}:</strong> {{ $permission->name }}</p>
            <p><strong>{{ __('permissions.description') }}:</strong> {{ $permission->description ?: __('permissions.na') }}</p>
            <p><strong>{{ __('permissions.created_at') }}:</strong> {{ $permission->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('permissions.updated_at') }}:</strong> {{ $permission->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('permissions.edit', $permission->permission_id) }}" class="btn btn-warning">{{ __('permissions.edit') }}</a>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">{{ __('permissions.back_to_list') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ __('permissions.assigned_to_roles') }} ({{ $permission->roles->count() }})
        </div>
        <ul class="list-group list-group-flush">
            @forelse($permission->roles as $role)
                <li class="list-group-item">{{ $role->name }}</li>
            @empty
                <li class="list-group-item">{{ __('permissions.no_roles_assigned') }}</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection