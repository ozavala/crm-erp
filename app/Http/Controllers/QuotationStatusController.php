<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuotationStatusController extends Controller
{
    /**
     * Update the status of the specified quotation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Quotation $quotation)
    {
        Gate::authorize('edit-quotations'); // Or a more specific permission

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Accepted,Declined'],
        ]);

        // Prevent changing status if already invoiced or has an order
        if ($quotation->invoice()->exists() || $quotation->order()->exists()) {
            return back()->with('error', 'Cannot change status. Quotation is already linked to an order or invoice.');
        }

        $quotation->status = $validated['status'];
        $quotation->save();

        $message = 'Quotation status updated to ' . $validated['status'] . '.';

        return redirect()->route('quotations.show', $quotation->quotation_id)
                         ->with('success', $message);
    }
}