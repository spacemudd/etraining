<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Back\JasarahCenterCertificate;
use App\Models\Back\JasarahCenterCertificateRow;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeCertificate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JasarahCenterCertificateService
{
    public function generateAndStorePdf(JasarahCenterCertificateRow $row, JasarahCenterCertificate $certificate): void
    {
        if ($row->pdf_path) {
            return;
        }

        $course = $certificate->course;

        $traineeCertificate = TraineeCertificate::firstOrCreate([
            'course_id' => $course->id,
            'trainee_id' => $row->trainee_id,
        ]);

        $pdfContent = JasarahCenterNoticePdfService::generate(
            $row->trainee_name_en,
            $certificate->displayCourseTitle()
        );
        $s3Path = JasarahCenterNoticePdfService::s3Path(
            $row->jasarah_center_certificate_id,
            $row->identity_number,
            $row->trainee_name_en
        );

        Storage::disk('s3')->put($s3Path, $pdfContent);

        $row->update([
            'pdf_path' => $s3Path,
            'trainee_certificate_id' => $traineeCertificate->id,
        ]);
    }

    public function updateImportCounts(\App\Models\Back\JasarahCenterCertificate $certificate): void
    {
        $certificate->update([
            'matched_count' => $certificate->rows()->whereNotNull('trainee_id')->count(),
            'unmatched_count' => $certificate->rows()->whereNull('trainee_id')
                ->where('status', '!=', JasarahCenterCertificateRow::STATUS_FAILED)->count(),
            'failed_count' => $certificate->rows()->where('status', JasarahCenterCertificateRow::STATUS_FAILED)->count(),
        ]);
    }

    public function syncTraineeEnglishNameFromCsvAfterSend(JasarahCenterCertificateRow $row): void
    {
        if (!$row->trainee_id) {
            return;
        }

        $csvEnglishName = trim((string) $row->trainee_name_en);

        if ($csvEnglishName === '' || !preg_match('/[a-zA-Z]/', $csvEnglishName)) {
            return;
        }

        $trainee = $row->relationLoaded('trainee')
            ? $row->trainee
            : Trainee::withTrashed()->find($row->trainee_id);

        if (!$trainee || $trainee->hasEnglishName()) {
            return;
        }

        $trainee->update(['english_name' => $csvEnglishName]);

        Log::info('Updated trainee english_name from Jasarah Center certificate CSV', [
            'trainee_id' => $trainee->id,
            'jasarah_center_certificate_row_id' => $row->id,
        ]);
    }
}
