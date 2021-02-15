<?php

namespace App\Jobs;

use App\Models\Back\MonthlyInvoicingBatch;
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
        //
    }
}
