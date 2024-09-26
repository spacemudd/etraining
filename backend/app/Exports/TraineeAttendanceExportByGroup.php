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
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setARGB('FFFFE599');

        foreach ($this->trainees as $key => $trainee) {
            // Center the values in all columns
            $rowIndex = $key + 2; // Adjusting for header row
            $sheet->getStyle("A$rowIndex:E$rowIndex")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $cell = 'A' . $rowIndex; // Cell for certificate eligibility
            if ($trainee['attendance_percentage'] >= 70) {
                $sheet->getCell($cell)->setValue('يستحق');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF00FF00'); // Green
            } else {
                $sheet->getCell($cell)->setValue('لا يستحق');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFF0000'); // Red
            }

            // Set the attendance percentage in column B
            $percentageCell = 'B' . $rowIndex;
            $sheet->getCell($percentageCell)->setValue($trainee['attendance_percentage']);
            // Center alignment for the percentage cell
            $sheet->getStyle($percentageCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Set the present count in column C
            $presentCountCell = 'C' . $rowIndex;
            $sheet->getCell($presentCountCell)->setValue($trainee['present_count']);
            // Center alignment for the present count cell
            $sheet->getStyle($presentCountCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Set the absent count in column D
            $absentCountCell = 'D' . $rowIndex;
            $sheet->getCell($absentCountCell)->setValue($trainee['absent_count']);
            // Center alignment for the absent count cell
            $sheet->getStyle($absentCountCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Set the trainee name in column E
            $nameCell = 'E' . $rowIndex;
            $sheet->getCell($nameCell)->setValue($trainee['trainee_name']);
            // Center alignment for the trainee name cell
            $sheet->getStyle($nameCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
    }
}

