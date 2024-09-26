<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Facades\Excel;

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
        $sheet->getStyle('A1:E1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFFFE599');

        $sheet->getStyle('A2:A' . (count($this->trainees) + 1))->applyFromArray([
            'font' => [
                'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
            ],
        ]);

        foreach ($this->trainees as $key => $trainee) {
            $cell = 'A' . ($key + 2); 
            if ($trainee['attendance_percentage'] >= 70) {
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF00FF00'); 
            } else {
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFF0000');
            }
        }

        // إخفاء العمود غير المرغوب فيه (مثلاً، إذا كان في العمود F)
        // $sheet->getColumnDimension('F')->setVisible(false); // استبدل 'F' بحرف العمود الذي ترغب في إخفائه
    }
}


