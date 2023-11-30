<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\Back\InvoiceItem;
use App\Models\Back\Trainee;
use App\Notifications\NewInvoiceIssued;
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

        $company = Company::query()
            ->with(['trainees' => function($q) {

            }])
            ->findOrFail($company_id);

        $trainees = $company->trainees
            ->pluck('name', 'id');

        $deleted_trainees = $company->trainees->where('deleted_at', '!=', null)
            ->pluck('name', 'id');

        return Inertia::render('Back/Companies/Invoices/Create', [
            'company' => $company,
            'trainees' => $trainees,
            'deleted_trainees' => $deleted_trainees ?? [],
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
                // Should we override?
                $cost = Trainee::findOrFail($trainee_id)->override_training_costs;
                if ($cost !== null) {
                    $grand_total = Money::of($cost, 'SAR', new CustomContext(2), RoundingMode::HALF_UP);
                    $sub_total = $grand_total->multipliedBy(1 / (1 + InvoiceItem::DEFAULT_TAX), RoundingMode::HALF_UP);
                    $tax = $grand_total->minus($sub_total);
                    if ($grand_total->isEqualTo(0)) {
                        continue; // completely skip it.
                    }
                    $invoice = $company->invoices()->create(
                        array_merge([
                            'trainee_id' => $trainee_id,
                            'sub_total' => $sub_total->getAmount()->toFloat(),
                            'tax' => $tax->getAmount()->toFloat(),
                            'grand_total' => $grand_total->getAmount()->toFloat(),
                        ], $validatedData)
                    );
                } else {
                    $grand_total = Money::of($validatedData['value_per_invoice'], 'SAR', new CustomContext(2), RoundingMode::HALF_UP);
                    $sub_total = $grand_total->multipliedBy(1 / (1 + InvoiceItem::DEFAULT_TAX), RoundingMode::HALF_UP);
                    $tax = $grand_total->minus($sub_total);
                    $invoice = $company->invoices()->create(
                        array_merge([
                            'trainee_id' => $trainee_id,
                            'sub_total' => $sub_total->getAmount()->toFloat(),
                            'tax' => $tax->getAmount()->toFloat(),
                            'grand_total' => $grand_total->getAmount()->toFloat(),
                        ], $validatedData)
                    );
                }

                $period = [
                    'start' => Carbon::parse($validatedData['from_date'])->format('Y-m-d'),
                    'end' => Carbon::parse($validatedData['to_date'])->format('Y-m-d'),
                ];

                $invoice->items()->create([
                    'name_en' => $company->name_en . ' - ' .__('words.training-costs-for-the-period-of', $period, 'en'),
                    'name_ar' => $company->name_ar . ' - ' .__('words.training-costs-for-the-period-of', $period, 'ar'),
                    'sub_total' => $sub_total->getAmount()->toFloat(),
                    'tax' => $tax->getAmount()->toFloat(),
                    'grand_total' => $grand_total->getAmount()->toFloat(),
                ]);

                AccountingLedgerBook::create([
                    'team_id' => $invoice->team_id,
                    'company_id' => $invoice->company_id,
                    'trainee_id' => $invoice->trainee_id,
                    'invoice_id' => $invoice->id,
                    'date' => now(),
                    'description' => $company->name_ar . ' - ' .__('words.training-costs-for-the-period-of', $period, 'ar'),
                    'reference'  => __('words.training-costs-for-the-period-of', $period, 'ar'),
                    'account_name' => $invoice->trainee->name,
                    'debit' => $invoice->grand_total,
                    'balance' => AccountingLedgerBook::getBalanceForTrainee($invoice->trainee->id) + $invoice->grand_total,
                ]);

                // TODO: Enable notification for later.
                // Trainee::withTrashed()->find($trainee_id)->notify(new NewInvoiceIssued());
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
                //Rule::exists('trainees', 'id')->where('company_id', $company_id),
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
                'numeric',
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

        $pdf = PDF::setOption('footer-html', resource_path('views/pdf/invoices/client-invoice-footer.html'))
            ->setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait')
            ->setOption('encoding','utf-8')
            ->setOption('dpi', 300)
            ->setOption('image-dpi', 300)
            ->setOption('lowquality', false)
            ->setOption('no-background', false)
            ->setOption('enable-internal-links', true)
            ->setOption('enable-external-links', true)
            ->setOption('javascript-delay', 1000)
            ->setOption('no-stop-slow-scripts', true)
            ->setOption('no-background', false)
            ->setOption('margin-left', 10)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 20)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)->loadView("pdf.invoices.company-report", [
                'company' => $company,
                'invoice_group' => $invoice_group,
                'invoices' => $invoices,
            ]);

        return $pdf->inline();
    }

    public function bulkPdf(Request $request, string $company_id)
    {
        $company = Company::findOrFail($company_id);

        $invoices = Invoice::query();

        $from_date = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : now()->startOfMonth()->toDateString();
        $to_date = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : now()->endOfMonth()->toDateString();

        $invoices = $invoices
            ->whereBetween('from_date', [$from_date, $to_date])
            ->get();

        $pdf = PDF::setOption('footer-html', resource_path('views/pdf/invoices/client-invoice-footer.html'))
            ->setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait')
            ->setOption('encoding','utf-8')
            ->setOption('dpi', 300)
            ->setOption('image-dpi', 300)
            ->setOption('lowquality', false)
            ->setOption('no-background', false)
            ->setOption('enable-internal-links', true)
            ->setOption('enable-external-links', true)
            ->setOption('javascript-delay', 1000)
            ->setOption('no-stop-slow-scripts', true)
            ->setOption('no-background', false)
            ->setOption('margin-left', 10)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 20)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)->loadView("pdf.invoices.bulk-company-report", [
                'company' => $company,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'invoices' => $invoices,
                'grand_total' => $invoices->sum('grand_total'),
            ]);


        return $pdf->inline();
    }
}
