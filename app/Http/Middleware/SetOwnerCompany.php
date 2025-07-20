<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\OwnerCompany;

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
            // Suponiendo que el usuario tiene relación ownerCompanies
            $companies = $user->ownerCompanies ?? collect();
            if ($companies->count() === 1) {
                Session::put('owner_company_id', $companies->first()->id);
            } elseif ($companies->count() > 1 && !Session::has('owner_company_id')) {
                // Si tiene varias y no hay selección, podrías redirigir a un selector o dejarlo en null
                Session::put('owner_company_id', $companies->first()->id); // Por defecto la primera
            }
        }
        return $next($request);
    }
} 