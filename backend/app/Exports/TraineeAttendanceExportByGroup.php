<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TraineeAttendanceExportByGroup implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $trainees;

    public function __construct($trainees)
    {
        $this->trainees = $trainees;
    }

    public function collection()
    {
        return collect($this->trainees);
    }

    public function headings(): array
    {
        return ['استحقاق الشهادة', 'نسبة الحضور', 'عدد الحضور', 'عدد الغياب', 'اسم المتدرب'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setARGB('FFFFE599');

        // Apply styles for the certificate eligibility column
        foreach ($this->trainees as $key => $trainee) {
            $cell = 'A' . ($key + 2); // Cell for certificate eligibility
            if ($trainee['attendance_percentage'] >= 70) {
                // Green for eligible
                $sheet->getCell($cell)->setValue('يستحق');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF00FF00');
            } else {
                // Red for not eligible
                $sheet->getCell($cell)->setValue('لا يستحق');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFF0000');
            }
            
            // Set the attendance percentage in column B
            $percentageCell = 'B' . ($key + 2);
            $sheet->getCell($percentageCell)->setValue($trainee['attendance_percentage']);
            $sheet->getStyle($percentageCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }
    }
}


