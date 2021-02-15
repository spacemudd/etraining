<?php

namespace App\Jobs;

use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Back\Trainee;
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
        // I want to go through all the trainees that have company_id field filled + and are active.
        // I want to see the date they signed up at. (last_billed_at)
        // I want to find out the per-day cost.
        // I want to bill them.

        Trainee::readyForBilling()->chunk(100, function ($trainees) {

        });
    }
}
