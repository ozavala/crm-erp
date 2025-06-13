<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\CrmUser;
use App\Http\Requests\StoreOpportunityRequest;
use App\Http\Requests\UpdateOpportunityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Opportunity::with(['lead', 'customer', 'assignedTo'])->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('first_name', 'like', "%{$searchTerm}%")->orWhere('last_name', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('lead', fn($lq) => $lq->where('title', 'like', "%{$searchTerm}%"));
            });
        }
        if ($request->filled('stage_filter')) {
            $query->where('stage', $request->input('stage_filter'));
        }

        $opportunities = $query->paginate(10)->withQueryString();
        $stages = Opportunity::$stages;
        return view('opportunities.index', compact('opportunities', 'stages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $stages = Opportunity::$stages;
        $leads = Lead::whereNotIn('status', ['Won', 'Lost'])->orderBy('title')->get(); // Active leads
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();
        $crmUsers = CrmUser::orderBy('full_name')->get();
        $selectedLeadId = $request->query('lead_id');
        $selectedCustomerId = null;
        if($selectedLeadId){
            $lead = Lead::find($selectedLeadId);
            if($lead && $lead->customer_id) {
                $selectedCustomerId = $lead->customer_id;
            }
        }

        return view('opportunities.create', compact('stages', 'leads', 'customers', 'crmUsers', 'selectedLeadId', 'selectedCustomerId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOpportunityRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by_user_id'] = Auth::id();

        Opportunity::create($validatedData);

        return redirect()->route('opportunities.index')
                         ->with('success', 'Opportunity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Opportunity $opportunity)
    {
        $opportunity->load(['lead', 'customer', 'assignedTo', 'createdBy', 'quotations']);
        return view('opportunities.show', compact('opportunity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Opportunity $opportunity)
    {
        $stages = Opportunity::$stages;
        $leads = Lead::orderBy('title')->get(); // Or filter active leads
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();
        $crmUsers = CrmUser::orderBy('full_name')->get();
        $opportunity->load(['lead', 'customer']);
        return view('opportunities.edit', compact('opportunity', 'stages', 'leads', 'customers', 'crmUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOpportunityRequest $request, Opportunity $opportunity)
    {
        $opportunity->update($request->validated());
        return redirect()->route('opportunities.index')
                         ->with('success', 'Opportunity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Opportunity $opportunity)
    {
        // Add checks if opportunity is linked to quotations, orders etc.
        $opportunity->delete();
        return redirect()->route('opportunities.index')
                         ->with('success', 'Opportunity deleted successfully.');
    }
}