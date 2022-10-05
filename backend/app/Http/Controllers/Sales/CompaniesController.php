<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompaniesController extends Controller
{
    public function show($id)
    {
        return Inertia::render('Sales/Companies/Show', [
            'company' => Company::findOrFail($id),
        ]);
    }
}
