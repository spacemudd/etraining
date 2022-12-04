<?php

namespace App\Jobs;

use App\Imports\CertificatesImportCsv;
use App\Models\Back\CertificatesImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class CertificateCsvImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $import;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CertificatesImport $import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->import->started_at = now();
        $this->import->save();
        Excel::import(new CertificatesImportCsv($this->import), $this->import->filepath);
    }

    public function fail($e)
    {
        $this->import->completed_at = now();
        $this->import->save();
    }
}
