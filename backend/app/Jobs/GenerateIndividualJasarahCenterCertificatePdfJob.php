<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Back\JasarahCenterCertificate;
use App\Models\Back\JasarahCenterCertificateRow;
use App\Services\JasarahCenterCertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateIndividualJasarahCenterCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 120;

    protected int $rowId;

    public function __construct(int $rowId)
    {
        $this->rowId = $rowId;
    }

    public function handle(JasarahCenterCertificateService $service): void
    {
        $row = JasarahCenterCertificateRow::with(['jasarahCenterCertificate.course'])->find($this->rowId);

        if (!$row || !$row->trainee_id || $row->status !== JasarahCenterCertificateRow::STATUS_PENDING || $row->pdf_path) {
            if ($row) {
                $service->refreshPdfGenerationStatus($row->jasarah_center_certificate_id);
            }

            return;
        }

        try {
            $service->generateAndStorePdf($row, $row->jasarahCenterCertificate);
        } catch (\Exception $e) {
            Log::error('Failed to generate Jasarah Center certificate PDF', [
                'row_id' => $row->id,
                'certificate_id' => $row->jasarah_center_certificate_id,
                'error' => $e->getMessage(),
            ]);

            $row->update([
                'status' => JasarahCenterCertificateRow::STATUS_FAILED,
                'error_message' => 'PDF generation failed: ' . $e->getMessage(),
            ]);
        }

        $service->refreshPdfGenerationStatus($row->jasarah_center_certificate_id);
    }
}
