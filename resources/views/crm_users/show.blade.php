@extends('layouts.app')

@section('title', 'CRM User Details')

@section('content')
<div class="container">
    <h1>{{ __('crm_users.details') }}</h1>

    <div class="card">
        <div class="card-header">
            {{ __('crm_users.user_id') }}: {{ $crmUser->user_id }}
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $crmUser->full_name }}</h5>
            <p class="card-text"><strong>{{ __('crm_users.username') }}:</strong> {{ $crmUser->username }}</p>
            <p class="card-text"><strong>{{ __('crm_users.email') }}:</strong> {{ $crmUser->email }}</p>
            <p class="card-text"><strong>{{ __('crm_users.email_verified_at') }}:</strong> {{ $crmUser->email_verified_at ? $crmUser->email_verified_at->format('Y-m-d H:i:s') : __('crm_users.not_verified') }}</p>
            <p class="card-text"><strong>{{ __('crm_users.created_at') }}:</strong> {{ $crmUser->created_at->format('Y-m-d H:i:s') }}</p>
            <p class="card-text"><strong>{{ __('crm_users.updated_at') }}:</strong> {{ $crmUser->updated_at->format('Y-m-d H:i:s') }}</p>
             <p class="card-text"><strong>{{ __('crm_users.roles') }}:</strong>
                @forelse($crmUser->roles as $role)
                    <span class="badge bg-info">{{ $role->name }}</span>
                @empty
                    No roles assigned.
                @endforelse
            </p>
        </div>
        <div class="card-footer">
            <a href="{{ route('crm-users.edit', $crmUser->user_id) }}" class="btn btn-warning">{{ __('crm_users.edit') }}</a>
            <form action="{{ route('crm-users.destroy', $crmUser->user_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('crm_users.delete') }}</button>
            </form>
            <a href="{{ route('crm-users.index') }}" class="btn btn-secondary">{{ __('crm_users.back_to_list') }}</a>
        </div>
    </div>
</div>
@endsection