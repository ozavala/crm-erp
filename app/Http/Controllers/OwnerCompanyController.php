<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerCompanyController extends Controller
{
    public function switch(Request $request)
    {
        $companyId = $request->input('owner_company_id');
        if (Auth::user()->ownerCompanies->pluck('id')->contains($companyId)) {
            session(['owner_company_id' => $companyId]);
        }
        return back();
    }
} 