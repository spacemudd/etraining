<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\InvoiceItem;
use App\Models\Back\Trainee;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Carbon\Carbon;
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

        $sub_total = Money::of($validatedData['value_per_invoice'], 'SAR', new CustomContext(2), RoundingMode::HALF_UP);
        $tax = $sub_total->multipliedBy(InvoiceItem::DEFAULT_TAX, RoundingMode::HALF_UP);
        $grand_total = $sub_total->plus($tax);

        DB::transaction(function () use ($request, $company, $validatedData, $sub_total, $tax, $grand_total) {
            foreach ($request->input('trainees') as $trainee_id) {
                $invoice = $company->invoices()->create(
                    array_merge([
                        'trainee_id' => $trainee_id,
                        'sub_total' => $sub_total->getAmount()->toFloat(),
                        'tax' => $tax->getAmount()->toFloat(),
                        'grand_total' => $grand_total->getAmount()->toFloat(),
                    ], $validatedData)
                );

                $period = [
                    'start' => Carbon::parse($validatedData['from_date'])->format('jS F'),
                    'end' => Carbon::parse($validatedData['to_date'])->format('jS F'),
                ];

                $invoice->items()->create([
                    'name_en' => __('words.training-costs-for-the-period-of', $period, 'en'),
                    'name_ar' => __('words.training-costs-for-the-period-of', $period, 'ar'),
                    'sub_total' => $sub_total->getAmount()->toFloat(),
                    'tax' => $tax->getAmount()->toFloat(),
                    'grand_total' => $grand_total->getAmount()->toFloat(),
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
            'from_date' => [
                'required',
                'date',
                'before:to_date'
            ],
            'to_date' => [
                'required',
                'date',
                'after:from_date'
            ],
            'value_per_invoice' => [
                'required',
                'integer',
                "min:1",
                "max:10000000",
            ],
        ]);
    }

    public function pdf(Request $request, string $company_id)
    {
        $company = Company::findOrFail($company_id);

        $invoice_group = $company->invoices()
            ->select('*')
            ->selectRaw('COUNT(id) as trainee_count')
            ->selectRaw('SUM(sub_total) as sub_total')
            ->selectRaw('SUM(tax) as tax')
            ->selectRaw('SUM(grand_total) as grand_total')
            ->where('from_date', $request->input('from_date'))
            ->where('to_date', $request->input('to_date'))
            ->where('created_by_id', $request->input('created_by_id', auth()->id()))
            ->whereDate('created_at', $request->input('created_at_date', now()->toDateString()))
            ->with(['created_by'])
            ->groupByRaw('from_date, to_date, created_by_id, DATE(created_at)')
            ->latest()
            ->firstOrFail();

        $invoices = $company->invoices()
            ->where('from_date', $request->input('from_date'))
            ->where('to_date', $request->input('to_date'))
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
