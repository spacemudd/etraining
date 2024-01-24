<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\CompanyAlias;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyAliasesController extends Controller
{
    public function index($company_id)
    {
        $company = Company::with('aliases')->findOrFail($company_id);
        return Inertia::render('Back/CompanyAliases/Index', [
            'company' => $company,
        ]);
    }

    public function store(Request $request, $company_id)
    {
        $request->validate([
            'alias' => 'required|string|max:255',
        ]);

        $company = Company::findOrFail($company_id);
        $company->aliases()->create([
            'alias' => $request->alias,
        ]);

        return redirect()->route('back.companies.aliases.index', $company_id)->with('success', 'Alias created.');
    }

    public function delete($company_id, $alias_id)
    {
        $alias = CompanyAlias::findOrFail($alias_id);
        $alias->delete();

        return redirect()->route('back.companies.aliases.index', $company_id)->with('success', 'Alias deleted.');
    }
}
