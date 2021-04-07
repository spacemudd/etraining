<?php

namespace App\Jobs;

use App\Exports\Back\TraineeExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ExportTraineesToExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $excelJob;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Back\ExportTraineesToExcelJobTracker $excelJob
     */
    public function __construct(\App\Models\Back\ExportTraineesToExcelJobTracker $excelJob)
    {
        $this->excelJob = $excelJob;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function handle()
    {
        Log::info('[ExportTraineesToExcelJob] Initiated for Team ID: '.$this->excelJob->team_id. ' and User: '.$this->excelJob->user->email);

        $this->excelJob->update(['started_at' => now()]);

        $fileName = uniqid('trainees-', true).'.xlsx';
        Excel::store(new TraineeExport(), $fileName, 'local');

        $this->excelJob->addMedia(storage_path('app/'.$fileName))
            ->withAttributes([
                'team_id' => $this->excelJob->team_id,
            ])
            ->toMediaCollection('excel');

        $this->excelJob->update(['finished_at' => now()]);

        Log::info('[ExportTraineesToExcelJob] Completed for Team ID: '.$this->excelJob->team_id. ' and User: '.$this->excelJob->user->email);
    }

    public function failed(Throwable $e)
    {
        Log::error('[ExportTraineesToExcelJob] Failed for Team ID: '.$this->excelJob->team_id. ' and User: '.$this->excelJob->user->email);
        $this->excelJob->failure_reason = $e->getMessage();
        $this->excelJob->save();
    }
}
