<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\OwnerCompany;
use Illuminate\Support\Facades\View;

class SetOwnerCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $companyId = Session::get('owner_company_id');

            // If no company is in the session, try to set one.
            if (!$companyId) {
                $companies = $user->ownerCompanies ?? collect();
                if ($companies->isNotEmpty()) {
                    $companyId = $companies->first()->id;
                    Session::put('owner_company_id', $companyId);
                }
            }

            // If a company ID is available, fetch the company and share it with all views.
            if ($companyId) {
                $activeCompany = OwnerCompany::find($companyId);
                View::share('activeCompany', $activeCompany);
            }
        }

        return $next($request);
    }
}