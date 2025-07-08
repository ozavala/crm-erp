@extends('layouts.app')

@section('title', 'User Roles')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('user_roles.title') }}</h1>
        <a href="{{ route('user-roles.create') }}" class="btn btn-primary">{{ __('user_roles.add_new_role') }}</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('user_roles.id') }}</th>
                <th>{{ __('user_roles.name') }}</th>
                <th>{{ __('user_roles.description') }}</th>
                <th>{{ __('user_roles.users') }}</th>
                <th>{{ __('user_roles.permissions') }}</th>
                <th>{{ __('user_roles.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($userRoles as $role)
                <tr>
                    <td>{{ $role->role_id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ Str::limit($role->description, 50) }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td>
                        <a href="{{ route('user-roles.show', $role->role_id) }}" class="btn btn-info btn-sm">{{ __('user_roles.view') }}</a>
                        <a href="{{ route('user-roles.edit', $role->role_id) }}" class="btn btn-warning btn-sm">{{ __('user_roles.edit') }}</a>
                        <form action="{{ route('user-roles.destroy', $role->role_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('user_roles.confirm_delete') }}')">{{ __('user_roles.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('user_roles.no_user_roles_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $userRoles->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection