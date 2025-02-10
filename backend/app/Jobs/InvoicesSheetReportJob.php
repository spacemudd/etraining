<?php

namespace App\Jobs;

use App\Exports\CourseAttendanceReportExport;
use App\Models\JobTracker;
use App\Reports\InvoicesReportFactory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoicesSheetReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 7200; 

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
        Log::info('[InvoicesSheetJob] Started. Tracker ID: '.$this->tracker->id);
        $this->tracker->update(['started_at' => now()]);

        $startTime = microtime(true); 

        try {
            $fileName = InvoicesReportFactory::new()
                ->setStartDate(Carbon::parse($this->tracker->metadata['date_from'])->startOfDay())
                ->setEndDate(Carbon::parse($this->tracker->metadata['date_to'])->endOfDay())
                ->setCompanyId($this->tracker->metadata['company_id'] ?? null)
                ->toExcel();

            $executionTime = microtime(true) - $startTime;
            Log::info('[InvoicesSheetJob] Report generated in ' . $executionTime . ' seconds');

            $this->tracker->addMedia(storage_path('app/'.$fileName))
                ->withAttributes([
                    'team_id' => $this->tracker->team_id,
                ])->toMediaCollection('excel');

            $this->tracker->update(['finished_at' => now()]);

            Log::info('[InvoicesSheetJob] Completed. Tracker ID: '.$this->tracker->id);
        } catch (Throwable $e) {
            $this->failed($e); 
        }
    }

    /**
     * Handle job failure.
     *
     * @param Throwable $e
     */
    public function failed(Throwable $e)
    {
        Log::error('[InvoicesSheetJob] Failed. Tracker ID: ' . $this->tracker->id . ' | Error: ' . $e->getMessage());
        Log::error($e->getTraceAsString()); 

        $this->tracker->update([
            'failure_reason' => $e->getMessage(),
            'finished_at' => now(),
        ]);
    }
}
