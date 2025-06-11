@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Permissions</h1>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">Add New Permission</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Roles Count</th>
                <th>Actions</th>
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
                        <a href="{{ route('permissions.show', $permission->permission_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('permissions.edit', $permission->permission_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('permissions.destroy', $permission->permission_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure? This will not detach roles automatically unless handled in controller.')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No permissions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $permissions->links() }}
    </div>
</div>
@endsection