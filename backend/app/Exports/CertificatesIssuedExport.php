<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeCustomCertificate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CertificatesIssuedExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            __('words.trainee-name'),
            __('words.email'),
            __('words.phone'),
            __('words.identity-number'),
            __('words.company'),
            __('words.certificate-title'),
            __('words.issue-date'),
            __('words.created-at'),
        ];
    }

    public function map($certificate): array
    {
        return [
            optional($certificate->trainee)->name,
            optional($certificate->trainee)->email,
            optional($certificate->trainee)->phone,
            optional($certificate->trainee)->identity_number,
            optional(optional($certificate->trainee)->company)->name_ar,
            $certificate->title,
            $certificate->issued_at_formatted,
            $certificate->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function collection()
    {
        return TraineeCustomCertificate::with(['trainee.company'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header row bold
            'A:H' => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 12,
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        if (app()->getLocale() === 'ar') {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(true);
                    // Auto-size columns
                    foreach(range('A','H') as $column) {
                        $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                    }
                },
            ];
        } else {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(false);
                    // Auto-size columns
                    foreach(range('A','H') as $column) {
                        $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                    }
                },
            ];
        }
    }
} 