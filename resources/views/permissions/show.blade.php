@extends('layouts.app')

@section('title', 'Permission Details')

@section('content')
<div class="container">
    <h1>{{ __('messages.Permission') }}: {{ $permission->name }}</h1>

    <div class="card mb-3">
        <div class="card-header">
            {{ __('permissions.permission_details') }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('messages.ID') }}:</strong> {{ $permission->permission_id }}</p>
            <p><strong>{{ __('messages.Name') }}:</strong> {{ $permission->name }}</p>
            <p><strong>{{ __('messages.Description') }}:</strong> {{ $permission->description ?: __('messages.N/A') }}</p>
            <p><strong>{{ __('messages.Created At') }}:</strong> {{ $permission->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('messages.Updated At') }}:</strong> {{ $permission->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('permissions.edit', $permission->permission_id) }}" class="btn btn-warning">{{ __('messages.Edit') }}</a>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">{{ __('messages.Back to List') }}</a>
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
                <li class="list-group-item">{{ __('messages.No results found') }}</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection