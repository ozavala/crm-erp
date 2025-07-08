@extends('layouts.app')

@section('title', 'CRM Users')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('crm_users.title') }}</h1>
        <a href="{{ route('crm-users.create') }}" class="btn btn-primary">{{ __('crm_users.add_new_user') }}</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('crm_users.id') }}</th>
                <th>{{ __('crm_users.username') }}</th>
                <th>{{ __('crm_users.full_name') }}</th>
                <th>{{ __('crm_users.email') }}</th>
                <th>{{ __('crm_users.roles') }}</th>
                <th>{{ __('crm_users.actions') }}</th>
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
                        @forelse($user->roles as $role)
                            <span class="badge bg-secondary">{{ $role->name }}</span>
                        @empty
                            No roles
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('crm-users.show', $user->user_id) }}" class="btn btn-info btn-sm">{{ __('crm_users.view') }}</a>
                        <a href="{{ route('crm-users.edit', $user->user_id) }}" class="btn btn-warning btn-sm">{{ __('crm_users.edit') }}</a>
                        <form action="{{ route('crm-users.destroy', $user->user_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('crm_users.confirm_delete') }}')">{{ __('crm_users.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('crm_users.no_users_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $crmUsers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection