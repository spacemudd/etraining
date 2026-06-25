<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Back\JasarahCenterCertificate;
use App\Models\Back\JasarahCenterCertificateRow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessJasarahCenterCertificateFinalizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 300;

    protected int $certificateId;

    public function __construct(int $certificateId)
    {
        $this->certificateId = $certificateId;
    }

    public function handle(): void
    {
        $certificate = JasarahCenterCertificate::find($this->certificateId);

        if (!$certificate) {
            return;
        }

        $certificate->rows()
            ->whereNotNull('trainee_id')
            ->where('status', JasarahCenterCertificateRow::STATUS_PENDING)
            ->whereNull('pdf_path')
            ->select('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    dispatch(new GenerateIndividualJasarahCenterCertificatePdfJob($row->id));
                }
            });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessJasarahCenterCertificateFinalizeJob failed', [
            'certificate_id' => $this->certificateId,
            'error' => $exception->getMessage(),
        ]);

        JasarahCenterCertificate::where('id', $this->certificateId)->update([
            'status' => JasarahCenterCertificate::STATUS_FAILED,
            'completed_at' => now(),
        ]);
    }
}
