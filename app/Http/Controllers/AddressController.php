<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Address::with('addressable')->latest('address_id');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            // Simple search on a few fields. Polymorphic search is more complex.
            $query->where(function ($q) use ($searchTerm) {
                $q->where('street_address_line_1', 'like', "%{$searchTerm}%")
                  ->orWhere('city', 'like', "%{$searchTerm}%")
                  ->orWhere('postal_code', 'like', "%{$searchTerm}%")
                  ->orWhere('address_type', 'like', "%{$searchTerm}%");
            });
        }

        $addresses = $query->paginate(15)->withQueryString();
        return view('addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     * Note: Creating a standalone address without context is unusual for polymorphic relations.
     * This form would require manual input of addressable_id and addressable_type.
     */
    public function create()
    {
        // You might want to pass a list of possible addressable types if needed
        // $addressableTypes = ['App\Models\Customer' => 'Customer', 'App\Models\Supplier' => 'Supplier', ...];
        return view('addresses.create', ['address' => new Address()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        $validatedData = $request->validated();

        // Handle 'is_primary' for the specific addressable entity
        if (isset($validatedData['is_primary']) && $validatedData['is_primary']) {
            Address::where('addressable_id', $validatedData['addressable_id'])
                   ->where('addressable_type', $validatedData['addressable_type'])
                   ->update(['is_primary' => false]);
        }

        Address::create($validatedData);

        // Redirecting to address index. A more user-friendly redirect would be to the parent entity's show page.
        return redirect()->route('addresses.index')
                         ->with('success', 'Address created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $address->load('addressable');
        return view('addresses.show', compact('address'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        return view('addresses.edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address)
    {
        $validatedData = $request->validated();

        // Handle 'is_primary' for the specific addressable entity
        if (isset($validatedData['is_primary']) && $validatedData['is_primary']) {
            Address::where('addressable_id', $address->addressable_id)
                   ->where('addressable_type', $address->addressable_type)
                   ->where('address_id', '!=', $address->address_id) // Exclude current address
                   ->update(['is_primary' => false]);
        } else if (!isset($validatedData['is_primary'])) { // If checkbox is unchecked
            $validatedData['is_primary'] = false;
        }

        $address->update($validatedData);

        return redirect()->route('addresses.index')
                         ->with('success', 'Address updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $address->delete();
        return redirect()->route('addresses.index')
                         ->with('success', 'Address deleted successfully.');
    }
}