<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CompanyInvoicesController extends Controller
{
    public function create(string $company_id): Response
    {
        $this->authorize('issue-monthly-invoices');

        $company = Company::query()->findOrFail($company_id);
        $trainees = $company->trainees()->pluck('name', 'id');

        return Inertia::render('Back/Companies/Invoices/Create', [
            'company' => $company,
            'trainees' => $trainees,
            'monthly_subscription_per_trainee' => $company->monthly_subscription_per_trainee,
        ]);
    }

    public function store(Request $request, string $company_id)
    {
        $this->authorize('issue-monthly-invoices');

        $company = Company::query()->findOrFail($company_id);

        $validatedData = $request->validate([
            'trainees' => [
                'required',
                'array',
            ],
            'trainees.*' => [
                'required',
                'string',
                Rule::exists('trainees', 'id')->where('company_id', $company->id),
                'bail',
            ],
            'month' => [
                'required',
                'numeric',
                'min:1',
                'max:12',
                Rule::unique('invoices', 'month')
                    ->where('year', now()->year)
                    ->where('company_id', $company->id)
                    ->whereIn('trainee_id', $request->input('trainees', [])),
            ],
            'year' => [
                'required',
                'numeric',
                'min:2021',
                'max:' . (now()->addYear()->year),
            ],
        ]);

        foreach ($request->input('trainees') as $trainee_id) {
            $company->invoices()->create(
                array_merge([
                    'trainee_id' => $trainee_id,
                    'amount' => $company->monthly_subscription_per_trainee,
                ], $validatedData)
            );
        }

        return redirect()->route('back.companies.show', $company_id);
    }
}
