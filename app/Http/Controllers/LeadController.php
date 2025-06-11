<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Customer;
use App\Models\CrmUser;
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
        // TODO: Add filtering by status, source, assigned user

        $leads = $query->paginate(10)->withQueryString();
        return view('leads.index', compact('leads'));
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
        return view('leads.show', compact('lead'));
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
}