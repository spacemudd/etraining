<?php

namespace App\Jobs;

use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Team;
use App\Notifications\TraineeTrainingFeesNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMonthlyInvoicingBatchNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batch;

    public $salesEmail;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Back\MonthlyInvoicingBatch $batch
     */
    public function __construct(MonthlyInvoicingBatch $batch)
    {
        $this->batch = $batch;
        $this->salesEmail = Team::find($batch->team_id)->sales_team_email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $just_one = 0;
        $this->batch->sale_invoices()->chunk(300, function($invoices) use (&$just_one) {
            $invoices->each(function($invoice) use (&$just_one) {
                if ($just_one === 2) {
                    return;
                }
                $invoice->billable->notify(new TraineeTrainingFeesNotification($invoice, $this->salesEmail));
                $just_one++;
            });
        });
    }
}
