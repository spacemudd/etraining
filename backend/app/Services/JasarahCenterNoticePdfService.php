<?php

declare(strict_types=1);

namespace App\Services;

use PDF;

class JasarahCenterNoticePdfService
{
    public static function generate(string $traineeNameEn, string $courseName): string
    {
        return PDF::setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait')
            ->setOption('encoding', 'utf-8')
            ->setOption('dpi', 300)
            ->setOption('zoom', 1)
            ->setOption('disable-smart-shrinking', true)
            ->loadView('pdf.jasarah-center.notice-of-attendance', [
                'trainee_name' => $traineeNameEn,
                'course_name' => $courseName,
                'date' => now()->format('d/m/Y'),
            ])
            ->output();
    }

    public static function s3Path(int $importId, string $identityNumber, string $traineeNameEn): string
    {
        $sanitizedName = preg_replace('/[^\w\-]/', '_', $traineeNameEn);
        $sanitizedName = substr($sanitizedName, 0, 100);

        return 'jasarah-center-certificates/' . $importId . '/' . $identityNumber . '_' . $sanitizedName . '.pdf';
    }
}
