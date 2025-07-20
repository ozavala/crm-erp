@extends('layouts.app')

@section('title', 'User Role Details')

@section('content')
<div class="container">
    <h1>{{ __('messages.Role') }}: {{ $userRole->name }}</h1>

    <div class="card mb-3">
        <div class="card-header">
            {{ __('user_roles.role_details') }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('messages.ID') }}:</strong> {{ $userRole->role_id }}</p>
            <p><strong>{{ __('messages.Name') }}:</strong> {{ $userRole->name }}</p>
            <p><strong>{{ __('messages.Description') }}:</strong> {{ $userRole->description ?: __('messages.N/A') }}</p>
            <p><strong>{{ __('messages.Created At') }}:</strong> {{ $userRole->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('messages.Updated At') }}:</strong> {{ $userRole->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('user-roles.edit', $userRole->role_id) }}" class="btn btn-warning">{{ __('messages.Edit') }}</a>
            <a href="{{ route('user-roles.index') }}" class="btn btn-secondary">{{ __('messages.Back to List') }}</a>
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
                <li class="list-group-item">{{ __('messages.No results found') }}</li>
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
                <li class="list-group-item">{{ __('messages.No results found') }}</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection