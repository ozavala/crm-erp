<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Customer;
use App\Models\CrmUser;
use App\Models\Activity; // Add Activity model
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    // Define statuses and sources for dropdowns
    protected $leadStatuses = [
        'New' => 'New',
        'Contacted' => 'Contacted',
        'Qualified' => 'Qualified',
        'Proposal Sent' => 'Proposal Sent',
        'Negotiation' => 'Negotiation',
        'Won' => 'Won',
        'Lost' => 'Lost',
        'On Hold' => 'On Hold',
    ];

    protected $leadSources = [
        'Website' => 'Website',
        'Referral' => 'Referral',
        'Cold Call' => 'Cold Call',
        'Advertisement' => 'Advertisement',
        'Event' => 'Event',
        'Other' => 'Other',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lead::with(['customer', 'assignedTo', 'createdBy'])->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('contact_name', 'like', "%{$searchTerm}%")
                  ->orWhere('contact_email', 'like', "%{$searchTerm}%")
                  ->orWhereHas('customer', function ($cq) use ($searchTerm) {
                      $cq->where('first_name', 'like', "%{$searchTerm}%")
                         ->orWhere('last_name', 'like', "%{$searchTerm}%")
                         ->orWhere('company_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('status_filter')) {
            $query->where('status', $request->input('status_filter'));
        }

        if ($request->filled('source_filter')) {
            $query->where('source', $request->input('source_filter'));
        }

        if ($request->filled('assigned_to_filter')) {
            $query->where('assigned_to_user_id', $request->input('assigned_to_filter'));
        }

        $leads = $query->paginate(10)->withQueryString();
        
        // Data for filter dropdowns
        $filterStatuses = $this->leadStatuses;
        $filterSources = $this->leadSources;
        $crmUsers = CrmUser::orderBy('full_name')->pluck('full_name', 'user_id');

        return view('leads.index', compact('leads', 'filterStatuses', 'filterSources', 'crmUsers'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = $this->leadStatuses;
        $sources = $this->leadSources;
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();
        $crmUsers = CrmUser::orderBy('full_name')->get();
        return view('leads.create', compact('statuses', 'sources', 'customers', 'crmUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeadRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by_user_id'] = Auth::id();

        Lead::create($validatedData);

        return redirect()->route('leads.index')
                         ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        $lead->load(['customer', 'assignedTo', 'createdBy']);
        return view('leads.show', compact('lead')); // Activities will be loaded via relationship in the view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $statuses = $this->leadStatuses;
        $sources = $this->leadSources;
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();
        $crmUsers = CrmUser::orderBy('full_name')->get();
        return view('leads.edit', compact('lead', 'statuses', 'sources', 'customers', 'crmUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($request->validated());
        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        // Add checks here if lead is linked to other critical data before deletion
        $lead->delete(); // This will soft delete due to the trait in the model
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    /**
     * Store a new activity for the specified lead.
     */
    public function storeActivity(Request $request, Lead $lead)
    {
        $request->validate([
            'type' => 'required|string|max:50|in:Call,Email,Meeting,Note,Other', // Define allowed types
            'description' => 'required|string',
            'activity_date' => 'nullable|date',
        ]);

        $lead->activities()->create([
            'user_id' => Auth::id(),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'activity_date' => $request->input('activity_date') ?: now(),
        ]);

        return back()->with('success', 'Activity logged successfully.');
    }

    /**
     * Convert the specified lead to a customer.
     */
    public function convertToCustomer(Request $request, Lead $lead)
    {
        // Prevent conversion if already Won or Lost, or if already has a customer_id and we don't want to override
        if (in_array($lead->status, ['Won', 'Lost'])) {
            return redirect()->route('leads.show', $lead)->with('error', 'Lead is already Won or Lost and cannot be converted again.');
        }

        $customer = null;
        // Check if an existing customer_id is provided (e.g., from a form if we want to allow selecting one)
        // For a simple conversion, we'll create a new one if not already linked.
        if ($lead->customer_id) {
            $customer = Customer::find($lead->customer_id);
            if (!$customer) {
                 return redirect()->route('leads.show', $lead)->with('error', 'Associated customer not found. Cannot convert.');
            }
        } else {
            // Create a new customer from lead details
            // Ensure lead has necessary contact info
            if (empty($lead->contact_name)) {
                return redirect()->route('leads.show', $lead)->with('error', 'Lead contact name is missing. Cannot create customer.');
            }
            // Split contact_name into first_name and last_name (simple split)
            $nameParts = explode(' ', $lead->contact_name, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; // Handle cases with only one name

            $customer = Customer::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $lead->contact_email,
                'phone_number' => $lead->contact_phone,
                'company_name' => $lead->customer ? $lead->customer->company_name : null, // Or a new field in Lead for company
                'status' => 'Active', // Default status for new customer
                'created_by_user_id' => Auth::id(),
                // Potentially copy address fields if they exist on the Lead model and are separate from Customer
            ]);
        }

        // Update lead
        $lead->customer_id = $customer->customer_id;
        $lead->status = 'Won'; // Or a specific "Converted" status if you add one
        $lead->save();

        return redirect()->route('customers.show', $customer->customer_id)->with('success', 'Lead successfully converted to customer. Lead status updated to Won.');
    }
}