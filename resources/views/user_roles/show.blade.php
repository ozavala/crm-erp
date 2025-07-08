@extends('layouts.app')

@section('title', 'User Role Details')

@section('content')
<div class="container">
    <h1>{{ __('user_roles.role') }}: {{ $userRole->name }}</h1>

    <div class="card mb-3">
        <div class="card-header">
            {{ __('user_roles.role_details') }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('user_roles.id') }}:</strong> {{ $userRole->role_id }}</p>
            <p><strong>{{ __('user_roles.name') }}:</strong> {{ $userRole->name }}</p>
            <p><strong>{{ __('user_roles.description') }}:</strong> {{ $userRole->description ?: __('user_roles.na') }}</p>
            <p><strong>{{ __('user_roles.created_at') }}:</strong> {{ $userRole->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('user_roles.updated_at') }}:</strong> {{ $userRole->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('user-roles.edit', $userRole->role_id) }}" class="btn btn-warning">{{ __('user_roles.edit') }}</a>
            <a href="{{ route('user-roles.index') }}" class="btn btn-secondary">{{ __('user_roles.back_to_list') }}</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            {{ __('user_roles.assigned_users') }} ({{ $userRole->users->count() }})
        </div>
        <ul class="list-group list-group-flush">
            @forelse($userRole->users as $user)
                <li class="list-group-item">{{ $user->full_name }} ({{ $user->username }})</li>
            @empty
                <li class="list-group-item">{{ __('user_roles.no_users_assigned') }}</li>
            @endforelse
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            {{ __('user_roles.assigned_permissions') }} ({{ $userRole->permissions->count() }})
        </div>
        <ul class="list-group list-group-flush">
            @forelse($userRole->permissions as $permission)
                <li class="list-group-item">{{ $permission->name }} <small class="text-muted">({{ $permission->description }})</small></li>
            @empty
                <li class="list-group-item">{{ __('user_roles.no_permissions_assigned') }}</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection