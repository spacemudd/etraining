<?php

namespace App\Http\Controllers;

use App\Models\Back\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyAllowedUsersController extends Controller
{
    public function index($companyId)
    {
        $this->authorize('restrict-user-companies-settings');

        return Inertia::render('Back/Companies/AllowedUsers/Index', [
            'company' => Company::with('allowed_users')->findOrFail($companyId),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $company = Company::findOrFail($request->company_id);
        $company->allowed_users()->attach(User::where('email', $request->email)->first()->id);

        return redirect()->route('back.companies.index');
    }

    public function delete($company_id, $id)
    {
        $this->authorize('restrict-user-companies-settings');

        $company = Company::findOrFail($company_id);
        $company->allowed_users()->detach($id);

        return redirect()->route('back.companies.show', $company_id);
    }
}
