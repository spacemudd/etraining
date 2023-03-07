<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompaniesNotificationController extends Controller
{
    /**
     * @param $company_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index($company_id)
    {
        $company = Company::findOrFail($company_id);
        return Inertia::render('Back/Companies/Notification/Index', compact('company'));
    }
}
