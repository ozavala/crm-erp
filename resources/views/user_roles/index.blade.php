@extends('layouts.app')

@section('title', 'User Roles')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('messages.User Roles') }}</h1>
        <a href="{{ route('user-roles.create') }}" class="btn btn-primary">{{ __('messages.Add New') }}</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('messages.ID') }}</th>
                <th>{{ __('messages.Name') }}</th>
                <th>{{ __('messages.Description') }}</th>
                <th>{{ __('messages.Users') }}</th>
                <th>{{ __('messages.Permissions') }}</th>
                <th>{{ __('messages.Actions') }}</th>
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
                        <a href="{{ route('user-roles.show', $role->role_id) }}" class="btn btn-info btn-sm">{{ __('messages.View') }}</a>
                        <a href="{{ route('user-roles.edit', $role->role_id) }}" class="btn btn-warning btn-sm">{{ __('messages.Edit') }}</a>
                        <form action="{{ route('user-roles.destroy', $role->role_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.Are you sure?') }}')">{{ __('messages.Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('messages.No results found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $userRoles->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection