<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For aggregate queries

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Lead statistics
        $leadStatusCounts = Lead::select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->pluck('total', 'status');

        $totalLeads = Lead::count();
        $activeLeads = Lead::whereNotIn('status', ['Won', 'Lost'])->count(); // Leads that are not yet closed

        // Recent leads (e.g., last 5 created)
        $recentLeads = Lead::with('assignedTo')
                           ->orderBy('created_at', 'desc')
                           ->take(5)
                           ->get();

        return view('dashboard', compact('leadStatusCounts', 'totalLeads', 'activeLeads', 'recentLeads'));
    }
}