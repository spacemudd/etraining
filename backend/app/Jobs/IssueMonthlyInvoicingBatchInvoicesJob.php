<?php

namespace App\Jobs;

use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Back\SaleInvoice;
use App\Models\MaxNumber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueMonthlyInvoicingBatchInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batch;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Back\MonthlyInvoicingBatch $batch
     */
    public function __construct(MonthlyInvoicingBatch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \DB::beginTransaction();
        $this->batch->sale_invoices()->chunk(100, function($invoices) {
            $invoices->each(function($invoice) {
                $this->issue_invoice($invoice);
            });
        });
        \DB::commit();
    }

    public function issue_invoice(SaleInvoice $invoice)
    {
        $maxNumber = MaxNumber::firstOrCreate([
            'team_id' => $this->batch->team_id,
            'name' => 'TI-'.now()->format('m'),
        ], [
            'team_id' => $this->batch->team_id,
            'name' => 'TI-'.now()->format('m'),
            'value' => 1000,
        ]);

        $number = ++$maxNumber->value;
        $invoiceNumber = $maxNumber->name.$number;

        $maxNumber->value = $number;
        $maxNumber->save();

        $invoice->number = $invoiceNumber;
        $invoice->status = SaleInvoice::STATUS_ISSUED;
        $invoice->save();

        return $invoice;
    }

    /**
     * Handle a job failure.
     *
     * @param $exception
     * @return void
     */
    public function failed($exception)
    {
        $this->batch->status = MonthlyInvoicingBatch::JOB_STATUS_FAILED;
        $this->batch->save();
    }
}
