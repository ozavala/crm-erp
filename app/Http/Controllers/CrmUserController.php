<?php

namespace App\Http\Controllers;

use App\Models\CrmUser;
use App\Http\Requests\StoreCrmUserRequest;
use App\Http\Requests\UpdateCrmUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CrmUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $crmUsers = CrmUser::paginate(10);
        return view('crm_users.index', compact('crmUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm_users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCrmUserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        CrmUser::create($validatedData);

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
        return view('crm_users.edit', compact('crmUser'));
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

        $crmUser->delete();
        return redirect()->route('crm-users.index')->with('success', 'CRM User deleted successfully.');
    }
}