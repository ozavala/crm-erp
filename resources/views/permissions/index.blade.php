@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('permissions.title') }}</h1>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">{{ __('permissions.add_new_permission') }}</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('permissions.id') }}</th>
                <th>{{ __('permissions.name') }}</th>
                <th>{{ __('permissions.description') }}</th>
                <th>{{ __('permissions.roles_count') }}</th>
                <th>{{ __('permissions.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $permission)
                <tr>
                    <td>{{ $permission->permission_id }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>{{ Str::limit($permission->description, 70) }}</td>
                    <td>{{ $permission->roles_count }}</td>
                    <td>
                        <a href="{{ route('permissions.show', $permission->permission_id) }}" class="btn btn-info btn-sm">{{ __('permissions.view') }}</a>
                        <a href="{{ route('permissions.edit', $permission->permission_id) }}" class="btn btn-warning btn-sm">{{ __('permissions.edit') }}</a>
                        <form action="{{ route('permissions.destroy', $permission->permission_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('permissions.confirm_delete') }}')">{{ __('permissions.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('permissions.no_permissions_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $permissions->links ('pagination::bootstrap-5') }}
    </div>
</div>
@endsection