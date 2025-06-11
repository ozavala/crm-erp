@extends('layouts.app')

@section('title', 'CRM User Details')

@section('content')
<div class="container">
    <h1>CRM User Details</h1>

    <div class="card">
        <div class="card-header">
            User ID: {{ $crmUser->user_id }}
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $crmUser->full_name }}</h5>
            <p class="card-text"><strong>Username:</strong> {{ $crmUser->username }}</p>
            <p class="card-text"><strong>Email:</strong> {{ $crmUser->email }}</p>
            <p class="card-text"><strong>Email Verified At:</strong> {{ $crmUser->email_verified_at ? $crmUser->email_verified_at->format('Y-m-d H:i:s') : 'Not verified' }}</p>
            <p class="card-text"><strong>Created At:</strong> {{ $crmUser->created_at->format('Y-m-d H:i:s') }}</p>
            <p class="card-text"><strong>Updated At:</strong> {{ $crmUser->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('crm-users.edit', $crmUser->user_id) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('crm-users.destroy', $crmUser->user_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <a href="{{ route('crm-users.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection