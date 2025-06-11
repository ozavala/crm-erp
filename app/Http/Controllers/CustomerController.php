<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use GuzzleHttp\Psr7\Request;
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
        $statuses = $this->customerStatuses;
        return view('customers.create', compact('statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by_user_id'] = Auth::id();

        Customer::create($validatedData);

        return redirect()->route('customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load('createdBy');
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $statuses = $this->customerStatuses;
        return view('customers.edit', compact('customer', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
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
}