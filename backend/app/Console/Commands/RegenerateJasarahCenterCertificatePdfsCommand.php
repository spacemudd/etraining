<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessJasarahCenterCertificateFinalizeJob;
use App\Models\Back\JasarahCenterCertificate;
use App\Models\Back\JasarahCenterCertificateRow;
use App\Services\JasarahCenterCertificateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateJasarahCenterCertificatePdfsCommand extends Command
{
    protected $signature = 'jasarah:regenerate-pdfs {certificateId : The Jasarah Center certificate import ID}';

    protected $description = 'Re-resolve English names from CSV/trainee profile and regenerate Jasarah Center certificate PDFs';

    public function handle(JasarahCenterCertificateService $service): int
    {
        $certificate = JasarahCenterCertificate::find($this->argument('certificateId'));

        if (!$certificate) {
            $this->error('Certificate import not found.');

            return self::FAILURE;
        }

        $csvNames = $service->parseCsvEnglishNames($certificate);
        $this->info('Loaded ' . count($csvNames) . ' English names from CSV.');

        $rows = $certificate->rows()
            ->whereNotNull('trainee_id')
            ->where('status', JasarahCenterCertificateRow::STATUS_PENDING)
            ->with('trainee')
            ->get();

        $updated = 0;
        $cleared = 0;

        foreach ($rows as $row) {
            $csvEnglishName = $csvNames[$row->identity_number] ?? null;
            $resolvedName = $service->resolveEnglishNameForRow($row, $csvEnglishName);

            if ($row->pdf_path) {
                if (Storage::disk('s3')->exists($row->pdf_path)) {
                    Storage::disk('s3')->delete($row->pdf_path);
                }
                $cleared++;
            }

            $row->update([
                'trainee_name_en' => $resolvedName,
                'pdf_path' => null,
                'error_message' => null,
            ]);

            $updated++;
        }

        $certificate->update([
            'status' => JasarahCenterCertificate::STATUS_PROCESSING,
            'completed_at' => null,
        ]);

        dispatch(new ProcessJasarahCenterCertificateFinalizeJob($certificate->id));

        $this->info("Updated {$updated} rows, cleared {$cleared} PDFs, and queued regeneration for import #{$certificate->id}.");

        return self::SUCCESS;
    }
}
