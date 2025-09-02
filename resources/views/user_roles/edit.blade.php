@extends('layouts.app')

@section('title', 'Edit User Role')

@section('content')
<div class="container">
    <h1>Edit User Role: {{ $userRole->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('user-roles.update', $userRole->role_id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $userRole->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $userRole->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <h5>Permissions</h5>
                    <p class="text-muted">Select the permissions that this role should have.</p>
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->permission_id }}" id="perm_{{ $permission->permission_id }}"
                                        {{ in_array($permission->permission_id, old('permissions', $assignedPermissions)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $permission->permission_id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Role</button>
                <a href="{{ route('user-roles.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection