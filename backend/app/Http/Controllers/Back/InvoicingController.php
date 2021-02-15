<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\MakeTraineesDraftInvoicesJob;
use App\Models\Back\FinancialSetting;
use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Back\Trainee;
use App\Services\MonthlyInvoicingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoicingController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Finance/Invoicing/Index');
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
     */
    public function store(Request $request)
    {
        $batch = MonthlyInvoicingBatch::create([
            'invoices_date' => now()->startOfMonth(),
            'period_from' => now()->subMonth()->startOfMonth(),
            'period_to' => now()->subMonth()->endOfMonth(),
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
        return Inertia::render('Back/Finance/Invoicing/Show', [
            'batch' => MonthlyInvoicingBatch::with('created_by')
                ->withSum('sale_invoices', 'grand_total')
                ->findOrFail($batch),
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($batch)
    {
        $monthlyBatch = MonthlyInvoicingBatch::findOrFail($batch);

        $monthlyBatch->status = MonthlyInvoicingBatch::STATUS_APPROVED;
        $monthlyBatch->save();

        return redirect()->route('back.finance.invoicing.show', $monthlyBatch->id);
    }
}
