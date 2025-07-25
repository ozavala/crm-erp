<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelBaseController;

class BaseCompanyController extends LaravelBaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * The ID of the currently active company context.
     *
     * @var int|null
     */
    protected $currentCompanyId;

    /**
     * BaseCompanyController constructor.
     */
    public function __construct()
    {
        // This middleware ensures that for every request handled by a child controller,
        // the current company context is retrieved from the session and stored in a property.
        $this->middleware(function ($request, $next) {
            $this->currentCompanyId = Session::get('current_company_id');
            return $next($request);
        });
    }

    /**
     * Authorize that the given model belongs to the current company context.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string $companyIdColumn The column name for the company foreign key.
     * @return void
     */
    protected function authorizeCompanyAccess(Model $model, string $companyIdColumn = 'owner_company_id')
    {
        if ($model->{$companyIdColumn} != $this->currentCompanyId) {
            abort(403, 'Unauthorized action.');
        }
    }
}