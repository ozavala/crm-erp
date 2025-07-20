<?php

namespace App\Http\Controllers;

use App\Models\CrmUser;
use App\Http\Requests\StoreCrmUserRequest;
use App\Http\Requests\UpdateCrmUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserRole;



class CrmUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $crmUsers = CrmUser::with('roles')->paginate(10);
        return view('crm_users.index', compact('crmUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = UserRole::orderBy('name')->get(); 
        return view('crm_users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCrmUserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        $crmUser= CrmUser::create($validatedData);

         if ($request->has('roles')) {
            $crmUser->roles()->sync($request->input('roles'));
        }

        return redirect()->route('crm-users.index')->with('success', 'CRM User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CrmUser $crmUser)
    {
        return view('crm_users.show', compact('crmUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CrmUser $crmUser)
    {
       $roles = UserRole::orderBy('name')->get();
        $assignedRoles = $crmUser->roles->pluck('role_id')->toArray();
        return view('crm_users.edit', compact('crmUser', 'roles', 'assignedRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCrmUserRequest $request, CrmUser $crmUser)
    {
        $validatedData = $request->validated();

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // Don't update password if not provided
        }

        $crmUser->update($validatedData);

         if ($request->has('roles')) {
            $crmUser->roles()->sync($request->input('roles'));
        } else {
            $crmUser->roles()->detach(); // Remove all roles if none are selected
        }

        return redirect()->route('crm-users.index')->with('success', 'CRM User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CrmUser $crmUser)
    {
        // Add any checks here, e.g., prevent deleting the logged-in user or last admin
        // For now, basic delete:
        // if (auth()->id() === $crmUser->user_id) {
        //     return redirect()->route('crm-users.index')->with('error', 'You cannot delete yourself.');
        // }
        $crmUser->roles()->detach(); // Detach roles before deleting user
        $crmUser->delete();
        return redirect()->route('crm-users.index')->with('success', 'CRM User deleted successfully.');
    }
}