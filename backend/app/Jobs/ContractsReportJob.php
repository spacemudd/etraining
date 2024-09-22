<?php

namespace App\Jobs;

use App\Models\JobTracker;
use App\Reports\ContractsReportFactory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContractsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tracker;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\JobTracker $tracker
     */
    public function __construct(JobTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception|\Throwable
     */
    public function handle()
    {
        Log::info('[ContractsReportJob] Started. Tracker ID: '.$this->tracker->id);

        $this->tracker->update(['started_at' => now()]);

        $fileName = ContractsReportFactory::new()
            ->setStartDate(Carbon::parse($this->tracker->metadata['date_from'])->startOfDay())
            ->setEndDate(Carbon::parse($this->tracker->metadata['date_to'])->endOfDay())
            ->toExcel();

        $this->tracker->addMedia(storage_path('app/'.$fileName))
            ->withAttributes([
                'team_id' => $this->tracker->team_id,
            ])->toMediaCollection('excel');

        $this->tracker->update(['finished_at' => now()]);

        Log::info('[ContractsReportJob] Completed. Tracker ID: '.$this->tracker->id);
    }

    public function failed(Throwable $e)
    {
        Log::info('[ContractsReportJob] Failed. Tracker ID: '.$this->tracker->id);
        $this->tracker->failure_reason = $e->getMessage();
        $this->tracker->save();
    }
}
