<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->is_super_admin) {
            $accounts = Account::all();
        } else {
            $accounts = Account::where('owner_company_id', auth()->user()->owner_company_id)->get();
        }
        return response()->json($accounts);
    }
}
