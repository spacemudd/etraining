<?php

namespace App\Jobs;

use App\Exports\CompaniesPaidInvoices2024Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ExportCompaniesPaidInvoices2024Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

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
        Log::info('[ExportCompaniesPaidInvoices2024Job] Initiated for Team ID: '.$this->excelJob->team_id. ' and User: '.$this->excelJob->user->email);

        $this->excelJob->update(['started_at' => now()]);

        $fileName = uniqid('companies-paid-invoices-2024-', true).'.xlsx';
        Excel::store(new CompaniesPaidInvoices2024Export(), $fileName, 'local');

        $this->excelJob->addMedia(storage_path('app/'.$fileName))
            ->withAttributes([
                'team_id' => $this->excelJob->team_id,
            ])
            ->toMediaCollection('excel');

        $this->excelJob->update(['finished_at' => now()]);

        Log::info('[ExportCompaniesPaidInvoices2024Job] Completed for Team ID: '.$this->excelJob->team_id. ' and User: '.$this->excelJob->user->email);
    }

    public function failed(Throwable $e)
    {
        Log::error('[ExportCompaniesPaidInvoices2024Job] Failed for Team ID: '.$this->excelJob->team_id. ' and User: '.$this->excelJob->user->email);
        $this->excelJob->failure_reason = $e->getMessage();
        $this->excelJob->save();
    }
}

