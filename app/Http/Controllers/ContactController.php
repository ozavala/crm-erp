<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;


class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $contacts = Contact::with('customer')->latest()->paginate(15);
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $contact = new Contact();
        $customers = Customer::orderBy('company_name')->get();
        // Pre-select customer if an ID is passed in the request
        $selectedCustomerId = request()->get('customer_id');

        return view('contacts.create', compact('contact', 'customers', 'selectedCustomerId'));
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();
        $data['created_by_user_id'] = Auth::id();

        $contact = Contact::create($data);

        // Redirect back to the customer's page for a better user experience
        return redirect()->route('customers.show', $contact->customer_id)
                         ->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        $contact->load('customer');
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $customers = Customer::orderBy('company_name')->get();
        $selectedCustomerId = $contact->customer_id;

        return view('contacts.edit', compact('contact', 'customers', 'selectedCustomerId'));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());

        return redirect()->route('customers.show', $contact->customer_id)
                         ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $customerId = $contact->customer_id;
        $contact->delete(); // Soft delete

        return redirect()->route('customers.show', $customerId)
                         ->with('success', 'Contact deleted successfully.');
    }
}

