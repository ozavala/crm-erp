@extends('layouts.app')

@section('title', 'Edit User Role')

@section('content')
<div class="container">
    <h1>Edit User Role: {{ $userRole->name }}</h1>

    <form action="{{ route('user-roles.update', $userRole->role_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Role Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $userRole->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $userRole->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Permissions assignment will be added here later --}}
         <div class="mb-3">
            <label class="form-label">Permissions</label>
            @foreach($permissions as $permission)
                <div class="form-check">
                    <input class="form-check-input @error('permissions.'.$permission->permission_id) is-invalid @enderror"
                           type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->permission_id }}"
                           id="permission_{{ $permission->permission_id }}"
                           {{ (is_array(old('permissions')) && in_array($permission->permission_id, old('permissions'))) || (isset($assignedPermissions) && in_array($permission->permission_id, $assignedPermissions) && !is_array(old('permissions'))) ? 'checked' : '' }}>
                    <label class="form-check-label" for="permission_{{ $permission->permission_id }}">{{ $permission->name }}</label>
                </div>
            @endforeach
            @error('permissions.*') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Role</button>
        <a href="{{ route('user-roles.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection