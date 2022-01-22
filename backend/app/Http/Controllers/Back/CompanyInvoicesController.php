<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use PDF;

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

        $validatedData = $this->validateStoreRequest($request, $company->id);

        DB::transaction(function () use ($request, $company, $validatedData) {
            foreach ($request->input('trainees') as $trainee_id) {
                $invoice = $company->invoices()->create(
                    array_merge([
                        'trainee_id' => $trainee_id,
                        'total_amount' => $company->monthly_subscription_per_trainee,
                    ], $validatedData)
                );

                $invoice->items()->create([
                    'name' => 'Monthly Subscription Fees',
                    'amount' => $company->monthly_subscription_per_trainee,
                    'tax' => round($company->monthly_subscription_per_trainee * 0.15, 2),
                ]);
            }
        });

        return redirect()->route('back.companies.show', $company_id);
    }

    private function validateStoreRequest(Request $request, string $company_id): array
    {
        return $request->validate([
            'trainees' => [
                'required',
                'array',
            ],
            'trainees.*' => [
                'required',
                'string',
                Rule::exists('trainees', 'id')->where('company_id', $company_id),
                'bail',
            ],
            'month' => [
                'required',
                'numeric',
                'min:1',
                'max:12',
                Rule::unique('invoices', 'month')
                    ->where('year', now()->year)
                    ->where('company_id', $company_id)
                    ->whereIn('trainee_id', $request->input('trainees', [])),
            ],
            'year' => [
                'required',
                'numeric',
                'min:2021',
                'max:' . (now()->addYear()->year),
            ],
        ]);
    }

    public function pdf(Request $request, string $company_id)
    {
        $company = Company::findOrFail($company_id);

        $invoice_group = $company->invoices()
            ->select('*')
            ->selectRaw('COUNT(id) as trainee_count')
            ->selectRaw('SUM(total_amount) as grand_total')
            ->where('month', $request->input('month', now()->month))
            ->where('year', $request->input('year', now()->year))
            ->where('created_by_id', $request->input('created_by_id', auth()->id()))
            ->whereDate('created_at', $request->input('created_at_date', now()->toDateString()))
            ->with(['created_by'])
            ->groupByRaw('month, year, created_by_id, DATE(created_at)')
            ->latest()
            ->firstOrFail();

        $invoices = $company->invoices()
            ->where('month', $request->input('month', now()->month))
            ->where('year', $request->input('year', now()->year))
            ->where('created_by_id', $request->input('created_by_id', auth()->id()))
            ->whereDate('created_at', $request->input('created_at_date', now()->toDateString()))
            ->get();

        $pdf = PDF::loadView("pdf.invoices.company-report", [
            'company' => $company,
            'invoice_group' => $invoice_group,
            'invoices' => $invoices,
        ]);

        return $pdf->inline();
    }
}
