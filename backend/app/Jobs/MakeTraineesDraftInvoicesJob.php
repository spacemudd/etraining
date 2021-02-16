<?php

namespace App\Jobs;

use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Back\SaleInvoice;
use App\Models\Back\SaleInvoiceLine;
use App\Models\Back\Trainee;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MakeTraineesDraftInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public MonthlyInvoicingBatch $batch;

    public Money $costPerMonth;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Back\MonthlyInvoicingBatch $batch
     * @param $minorCostPerMonth
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function __construct(MonthlyInvoicingBatch $batch, $minorCostPerMonth)
    {
        $this->batch = $batch;
        $this->costPerMonth = Money::of($minorCostPerMonth, 'SAR');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        // I want to go through all the trainees that have company_id field filled + and are active.
        // I want to see the date they signed up at. (last_billed_at)
        // I want to find out the per-day cost.
        // I want to bill them.

        \DB::beginTransaction();

        Trainee::readyForBilling()->chunk(100, function ($trainees) {
            $trainees->each(function ($trainee) {
                $this->issue_draft_invoice_for_trainee($trainee);
            });
            $this->batch->refresh()->progress += $trainees->count();
            $this->batch->save();
        });

        \DB::commit();
    }

    /**
     *
     * @param \App\Models\Back\Trainee $trainee
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function issue_draft_invoice_for_trainee(Trainee $trainee)
    {
        $invoice = new SaleInvoice();
        $invoice->team_id = $this->batch->team_id;
        $invoice->monthly_invoicing_batch_id = $this->batch->id;
        $invoice->number = null;
        $invoice->issued_at = $this->batch->invoices_date;
        $invoice->billable_id = $trainee->id;
        $invoice->billable_type = Trainee::class;
        $invoice->status = SaleInvoice::STATUS_DRAFT;
        $invoice->save();

        // Calculate cost.
        $taxTotal = $this->costPerMonth->multipliedBy(0.15, RoundingMode::HALF_UP);
        $grandTotal = $this->costPerMonth->plus($taxTotal);

        $invoiceLine = new SaleInvoiceLine();
        $invoiceLine->team_id = $this->batch->team_id;
        $invoiceLine->sale_invoice_id = $invoice->id;
        $invoiceLine->description = 'Training fees - رسوم تدريب';
        $invoiceLine->qty = 1;
        $invoiceLine->sub_total = $this->costPerMonth->getMinorAmount()->toInt();
        $invoiceLine->tax_total = $taxTotal->getMinorAmount()->toInt();
        $invoiceLine->grand_total = $grandTotal->getMinorAmount()->toInt();
        $invoiceLine->save();

        // Update invoice header.
        $invoice->sub_total = $invoiceLine->sub_total;
        $invoice->tax_total = $invoiceLine->tax_total;
        $invoice->grand_total = $invoiceLine->grand_total;
        $invoice->save();
    }
}
