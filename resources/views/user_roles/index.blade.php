@extends('layouts.app')

@section('title', 'User Roles')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('User Roles') }}</h1>
        <a href="{{ route('user-roles.create') }}" class="btn btn-primary">{{ __('Add New Role') }}</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Users') }}</th>
                <th>{{ __('Permissions') }}</th>
                <th>{{ __('Actions') }}</th>
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
                        <a href="{{ route('user-roles.show', $role->role_id) }}" class="btn btn-info btn-sm">{{ __('View') }}</a>
                        <a href="{{ route('user-roles.edit', $role->role_id) }}" class="btn btn-warning btn-sm">{{ __('Edit') }}</a>
                        <form action="{{ route('user-roles.destroy', $role->role_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure? This will not detach users or permissions automatically unless handled in controller.')">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No user roles found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $userRoles->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection