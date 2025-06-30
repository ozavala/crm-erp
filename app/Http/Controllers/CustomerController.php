<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Address; // Add this
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request; // Corrected import
use Illuminate\Support\Facades\Auth;


class CustomerController extends Controller
{
    
    protected $customerStatuses = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
        'Lead' => 'Lead',
        'Prospect' => 'Prospect',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       Gate::authorize('view-customers');
       $query = Customer::with('createdBy')->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('company_name', 'like', "%{$searchTerm}%");
            });
        } // Consider adding status to search: ->orWhere('status', 'like', "%{$searchTerm}%")
        $customers = $query->paginate(10)->withQueryString(); // withQueryString to keep search params on pagination
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-customers');
        $statuses = $this->customerStatuses;
        return view('customers.create', compact('statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        Gate::authorize('create-customers');
        $validatedData = $request->validated();
        $validatedData['created_by_user_id'] = Auth::id();
        
        $customerData = collect($validatedData)->except(['addresses'])->all();
        $customer = Customer::create($customerData);

        $this->syncAddresses($customer, $request->input('addresses', []));
        
        return redirect()->route('customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        Gate::authorize('view-customers');
        $customer->load(['createdBy', 'addresses']);
        $customer_contacts =  $customer->contacts->where('contactable_type', 'App\Models\Customer')->where('contactable_id', $customer->customer_id);
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        Gate::authorize('edit-customers');
        $statuses = $this->customerStatuses;
        $customer->load('addresses'); // Load addresses for the form
        return view('customers.edit', compact('customer', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        Gate::authorize('edit-customers');
        $validatedData = $request->validated();
        $customerData = collect($validatedData)->except(['addresses'])->all();
        $customer->update($customerData);

        $this->syncAddresses($customer, $request->input('addresses', []));
        return redirect()->route('customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        Gate::authorize('delete-customers');
        // Add checks here if customer is linked to orders, invoices, etc.
        // For example:
        // if ($customer->orders()->exists()) {
        //     return redirect()->route('customers.index')
        //                      ->with('error', 'Cannot delete customer. They have existing orders.');
        // }

        try {
            $customer->delete();
            return redirect()->route('customers.index')
                             ->with('success', 'Customer deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle potential foreign key constraint violations if not handled by DB or model
            return redirect()->route('customers.index')
                             ->with('error', 'Could not delete customer. They might be associated with other records.');
        }
    }

    /**
     * Sync customer addresses.
     * For now, this handles a single address block from the form, intended as primary.
     * Can be expanded to handle multiple address blocks.
     */
    protected function syncAddresses(Customer $customer, array $addressesData)
    {
        // Assuming $addressesData is an array of address attributes for now.
        // If handling multiple, this would be an array of arrays.
        // For this iteration, we'll assume one address block is passed.

        if (!empty($addressesData)) {
            $addressInput = $addressesData[0] ?? null; // Get the first (and currently only) address block

            if ($addressInput && !empty($addressInput['street_address_line_1'])) {
                // If this address is marked as primary, unmark other primary addresses
                if (!empty($addressInput['is_primary'])) {
                    $customer->addresses()->where('is_primary', true)->update(['is_primary' => false]);
                }

                // Update or create the address
                // If an ID is passed, update; otherwise, create.
                // For simplicity, we'll create or update the first address or a new one.
                // A more robust solution for multiple addresses would involve checking existing address IDs.
                $customer->addresses()->updateOrCreate(
                    ['address_id' => $addressInput['address_id'] ?? null], // Condition to find existing
                    $addressInput // Data to update or create
                );
            }
        }
    }
}