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
                $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
                
                $rowIndex = $key + 2;
            
                if ($attendanceNumeric >= 70) {
                    $sheet->getCell("A$rowIndex")->setValue('يستحق');
                    $sheet->getStyle("A$rowIndex")->getFont()->getColor()->setARGB('FF00FF00'); 
                } else {
                    $sheet->getCell("A$rowIndex")->setValue('لا يستحق');
                    $sheet->getStyle("A$rowIndex")->getFont()->getColor()->setARGB('FFFF0000'); 
                }
            
                $sheet->getCell("B$rowIndex")->setValue($trainee['attendance_percentage']);
                $sheet->getCell("C$rowIndex")->setValue($trainee['present_count']);
                $sheet->getCell("D$rowIndex")->setValue($trainee['absent_count']);
                $sheet->getCell("E$rowIndex")->setValue($trainee['trainee_name']);
            }
            
    }
}

