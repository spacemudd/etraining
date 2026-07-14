<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Back\JasarahCenterCertificate;
use App\Models\Back\JasarahCenterCertificateRow;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeCertificate;
use App\Support\IdentityNumberNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JasarahCenterCertificateService
{
    public static function resolveImportEnglishName(string $csvName, ?Trainee $trainee): string
    {
        $csvName = trim($csvName);

        if (
            $csvName !== ''
            && self::containsLatinLetters($csvName)
            && !Trainee::isPlaceholderEnglishName($csvName)
        ) {
            return $csvName;
        }

        if ($trainee && $trainee->hasEnglishName()) {
            return trim($trainee->english_name);
        }

        return Trainee::isPlaceholderEnglishName($csvName) ? '' : $csvName;
    }

    public function resolveEnglishNameForRow(JasarahCenterCertificateRow $row, ?string $csvEnglishName = null): string
    {
        $row->loadMissing('trainee');

        $csvName = $csvEnglishName ?? trim((string) $row->trainee_name_en);

        return self::resolveImportEnglishName($csvName, $row->trainee);
    }

    /**
     * @return array<string, string>
     */
    public function parseCsvEnglishNames(JasarahCenterCertificate $certificate): array
    {
        if (!$certificate->csv_path || !Storage::disk('s3')->exists($certificate->csv_path)) {
            return [];
        }

        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            return [];
        }

        fwrite($handle, Storage::disk('s3')->get($certificate->csv_path));
        rewind($handle);

        $namesByIdentity = [];
        $header = null;
        $idIndex = null;
        $nameIndex = null;

        while (($data = fgetcsv($handle)) !== false) {
            if ($header === null) {
                $header = array_map(fn ($col) => strtolower(trim($col)), $data);
                $idIndex = $this->findColumnIndex($header, ['id', 'رقم الهوية', 'name']);
                $nameIndex = $this->findColumnIndex(
                    $header,
                    ['english_name', 'name'],
                    $idIndex !== null ? [$idIndex] : []
                );

                continue;
            }

            if ($idIndex === null) {
                continue;
            }

            $identityNumber = isset($data[$idIndex]) ? IdentityNumberNormalizer::normalize(trim($data[$idIndex])) : '';
            $englishName = ($nameIndex !== null && isset($data[$nameIndex])) ? trim($data[$nameIndex]) : '';

            if ($identityNumber !== '' && is_numeric($identityNumber)) {
                $namesByIdentity[$identityNumber] = $englishName;
            }
        }

        fclose($handle);

        return $namesByIdentity;
    }

    public function generateAndStorePdf(JasarahCenterCertificateRow $row, JasarahCenterCertificate $certificate): void
    {
        $previousPath = $row->pdf_path;

        $course = $certificate->course;

        $traineeCertificate = TraineeCertificate::firstOrCreate([
            'course_id' => $course->id,
            'trainee_id' => $row->trainee_id,
        ]);

        $traineeNameEn = $this->resolveEnglishNameForRow($row);

        if (
            $traineeNameEn === ''
            || !self::containsLatinLetters($traineeNameEn)
            || Trainee::isPlaceholderEnglishName($traineeNameEn)
        ) {
            throw new \RuntimeException('No English name available for trainee');
        }

        $pdfContent = JasarahCenterNoticePdfService::generate(
            $traineeNameEn,
            $certificate->displayCourseTitle()
        );
        $s3Path = JasarahCenterNoticePdfService::s3Path(
            $row->jasarah_center_certificate_id,
            $row->identity_number,
            $traineeNameEn
        );

        Storage::disk('s3')->put($s3Path, $pdfContent);

        $row->update([
            'trainee_name_en' => $traineeNameEn,
            'pdf_path' => $s3Path,
            'trainee_certificate_id' => $traineeCertificate->id,
        ]);

        if ($previousPath && $previousPath !== $s3Path) {
            Storage::disk('s3')->delete($previousPath);
        }
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

    public function refreshPdfGenerationStatus(int $certificateId): void
    {
        $certificate = JasarahCenterCertificate::find($certificateId);

        if (!$certificate || $certificate->status !== JasarahCenterCertificate::STATUS_PROCESSING) {
            return;
        }

        $stillGenerating = $certificate->rows()
            ->whereNotNull('trainee_id')
            ->where('status', JasarahCenterCertificateRow::STATUS_PENDING)
            ->whereNull('pdf_path')
            ->exists();

        if ($stillGenerating) {
            return;
        }

        $this->updateImportCounts($certificate);

        $hasSendableRows = $certificate->rows()
            ->whereNotNull('trainee_id')
            ->whereNotNull('pdf_path')
            ->where('status', JasarahCenterCertificateRow::STATUS_PENDING)
            ->exists();

        $certificate->update([
            'status' => $hasSendableRows
                ? JasarahCenterCertificate::STATUS_READY_TO_SEND
                : JasarahCenterCertificate::STATUS_FAILED,
            'completed_at' => now(),
        ]);
    }

    public function syncTraineeEnglishNameFromCsv(Trainee $trainee, string $csvEnglishName, ?string $contextRowId = null): bool
    {
        $csvEnglishName = trim($csvEnglishName);

        if (
            $csvEnglishName === ''
            || !self::containsLatinLetters($csvEnglishName)
            || Trainee::isPlaceholderEnglishName($csvEnglishName)
        ) {
            return false;
        }

        if ($trainee->hasEnglishName()) {
            return false;
        }

        $trainee->update(['english_name' => $csvEnglishName]);

        Log::info('Updated trainee english_name from Jasarah Center certificate CSV', [
            'trainee_id' => $trainee->id,
            'jasarah_center_certificate_row_id' => $contextRowId,
        ]);

        return true;
    }

    public function syncTraineeEnglishNameFromCsvAfterSend(JasarahCenterCertificateRow $row): void
    {
        if (!$row->trainee_id) {
            return;
        }

        $trainee = $row->relationLoaded('trainee')
            ? $row->trainee
            : Trainee::withTrashed()->find($row->trainee_id);

        if (!$trainee) {
            return;
        }

        $this->syncTraineeEnglishNameFromCsv($trainee, (string) $row->trainee_name_en, (string) $row->id);
    }

    public function syncMatchedRowsEnglishNamesFromCsv(JasarahCenterCertificate $certificate): void
    {
        $csvNames = $this->parseCsvEnglishNames($certificate);

        $certificate->rows()
            ->whereNotNull('trainee_id')
            ->with(['trainee' => fn ($query) => $query->withTrashed()])
            ->chunkById(100, function ($rows) use ($csvNames) {
                foreach ($rows as $row) {
                    if (!$row->trainee) {
                        continue;
                    }

                    $csvEnglishName = $csvNames[$row->identity_number] ?? (string) $row->trainee_name_en;
                    $this->syncTraineeEnglishNameFromCsv($row->trainee, $csvEnglishName, (string) $row->id);
                }
            });
    }

    private static function containsLatinLetters(string $value): bool
    {
        return (bool) preg_match('/[a-zA-Z]/', $value);
    }

    /**
     * @param  array<int, string>  $header
     * @param  array<int, string>  $candidates
     * @param  array<int, int>  $excludeIndexes
     */
    private function findColumnIndex(array $header, array $candidates, array $excludeIndexes = []): ?int
    {
        foreach ($candidates as $candidate) {
            $index = array_search(strtolower($candidate), $header, true);
            if ($index !== false && !in_array((int) $index, $excludeIndexes, true)) {
                return (int) $index;
            }
        }

        return null;
    }
}
