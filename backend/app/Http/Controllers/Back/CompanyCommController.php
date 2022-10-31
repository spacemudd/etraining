<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyCommController extends Controller
{
    public function create($company_id)
    {
        return Inertia::render('Back/CompaniesComm/Create', [
            'company' => Company::findOrFail($company_id),
        ]);
    }
}
