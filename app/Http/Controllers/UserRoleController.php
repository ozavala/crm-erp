<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\Http\Requests\StoreUserRoleRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\Permission; // Uncomment when Permission model is created

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userRoles = UserRole::withCount('users', 'permissions')->paginate(10);
        return view('user_roles.index', compact('userRoles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $permissions = Permission::all(); // Uncomment when Permission model is created
        // return view('user_roles.create', compact('permissions')); // Adjust view name
        $permissions = Permission::orderBy('name')->get();
        return view('user_roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRoleRequest $request)
    {
        $userRole = UserRole::create($request->only('name', 'description'));

         if ($request->has('permissions')) { // Uncomment when permissions are handled
             $userRole->permissions()->sync($request->input('permissions'));
         }

        return redirect()->route('user-roles.index')->with('success', 'User Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserRole $userRole)
    {
        $userRole->load('users', 'permissions'); // Eager load relationships
        return view('user_roles.show', compact('userRole'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserRole $userRole)
    {
        // $permissions = Permission::all(); // Uncomment when Permission model is created
        // $assignedPermissions = $userRole->permissions->pluck('permission_id')->toArray(); // Uncomment
        // return view('user_roles.edit', compact('userRole', 'permissions', 'assignedPermissions')); // Adjust view name
        $permissions = Permission::orderBy('name')->get();
        $assignedPermissions = $userRole->permissions->pluck('permission_id')->toArray();
        return view('user_roles.edit', compact('userRole', 'permissions', 'assignedPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRoleRequest $request, UserRole $userRole)
    {
        $userRole->update($request->only('name', 'description'));

        // if ($request->has('permissions')) { // Uncomment when permissions are handled
        //     $userRole->permissions()->sync($request->input('permissions'));
        // } else {
        //     $userRole->permissions()->detach(); // Remove all permissions if none are selected
        // }
        if ($request->has('permissions')) {
            $userRole->permissions()->sync($request->input('permissions'));
        } else {
            $userRole->permissions()->detach(); // Remove all permissions if none are selected
        }

        return redirect()->route('user-roles.index')->with('success', 'User Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRole $userRole)
    {
        // Optional: Check if the role is assigned to any users before deleting
        if ($userRole->users()->count() > 0) {
            return redirect()->route('user-roles.index')->with('error', 'Cannot delete role. It is assigned to one or more users.');
        }

        // Optional: Detach all permissions before deleting the role
        // $userRole->permissions()->detach();
        $userRole->permissions()->detach(); // Detach all permissions before deleting the rol
        $userRole->delete();
        return redirect()->route('user-roles.index')->with('success', 'User Role deleted successfully.');
    }
}