<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Resources\MonthlyInvoicingBatchResource;
use App\Jobs\IssueMonthlyInvoicingBatchInvoicesJob;
use App\Jobs\MakeTraineesDraftInvoicesJob;
use App\Jobs\SendMonthlyInvoicingBatchNotificationsJob;
use App\Models\Back\FinancialSetting;
use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Back\Trainee;
use App\Services\MonthlyInvoicingService;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Inertia\Inertia;

class InvoicingController extends Controller
{
    public function index()
    {
        $monthlyInvoicingBatches = MonthlyInvoicingBatch::withCount('sale_invoices')->paginate(12);
        return Inertia::render('Back/Finance/Invoicing/Index', [
            'monthlyInvoicingBatches' => $monthlyInvoicingBatches,
        ]);
    }

    /**
     *
     */
    public function create()
    {
        return Inertia::render('Back/Finance/Invoicing/Create');
    }

    /**
     * Store a new monthly invoicing batch.
     * This is a collection of invoices for the current month.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'year' => 'required|string',
            'month' => 'required|string',
        ]);

        $batch = MonthlyInvoicingBatch::create([
            'company_id' => $request->company_id,
            'invoices_date' => now()->setYear($request->year)->setMonth($request->month)->startOfMonth(),
            'period_from' => now()->setYear($request->year)->setMonth($request->month)->startOfMonth(),
            'period_to' => now()->setYear($request->year)->setMonth($request->month)->endOfMonth(),
            'job_status' => MonthlyInvoicingBatch::JOB_STATUS_QUEUED,
            'status' => MonthlyInvoicingBatch::STATUS_DRAFT,
            'progress' => 0,
            'total' => Trainee::readyForBilling()->count(),
        ]);

        dispatch(new MakeTraineesDraftInvoicesJob($batch, FinancialSetting::first()->trainee_monthly_subscription));

        return redirect()->route('back.finance.invoicing.show', [
            'batch' => $batch->id,
        ]);
    }

    /**
     *
     * @param $batch MonthlyInvoicingBatch
     * @return \Inertia\Response
     */
    public function show($batch)
    {
        MonthlyInvoicingBatchResource::withoutWrapping();

        $invoicesBatch = MonthlyInvoicingBatch::with('created_by')
            ->withSum('sale_invoices', 'grand_total')
            ->withCount('sale_invoices')
            ->with(['sale_invoices' => function($q) {
                $q->with(['billable' => function($q) {
                    $q->with('company');
                }])->paginate(5);
            }])
            ->findOrFail($batch);

        if (request()->wantsJson()) {
            return new MonthlyInvoicingBatchResource($invoicesBatch);
        }

        return Inertia::render('Back/Finance/Invoicing/Show', [
            'batch' => new MonthlyInvoicingBatchResource($invoicesBatch),
        ]);
    }

    public function delete($batch)
    {
        MonthlyInvoicingBatch::findOrFail($batch)->delete();
        return redirect()->route('back.finance.invoicing.index');
    }

    /**
     *
     * @param $batch
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function approve($batch, Request $request)
    {
        $request->validate([
            'approved' => 'required|boolean',
        ]);

        if (!$request->approved) {
            return response()->json([
                'msg' => 'Please approve',
            ], 402);
        }

        $monthlyBatch = MonthlyInvoicingBatch::findOrFail($batch);

        throw_if($monthlyBatch->status !== MonthlyInvoicingBatch::STATUS_DRAFT, 'The batch must be in draft mode');

        $monthlyBatch->status = MonthlyInvoicingBatch::STATUS_APPROVED;
        $monthlyBatch->job_status = MonthlyInvoicingBatch::JOB_STATUS_COMMITTING_PROCESSING;
        $monthlyBatch->save();

        Bus::chain([
            new IssueMonthlyInvoicingBatchInvoicesJob($monthlyBatch),
            new SendMonthlyInvoicingBatchNotificationsJob($monthlyBatch),
        ])->dispatch();

        return redirect()->route('back.finance.invoicing.show', $monthlyBatch->id);
    }
}
