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
        // dd($this->trainees);
        return collect($this->trainees);
        // return collect($this->trainees)->sortByDesc(function ($trainee) {
        //     $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
        //     return ($attendanceNumeric >= 50 && $trainee['invoice_status'] == 'مدفوع') ? 1 : 0;
        // })->values();
    }

    public function headings(): array
    {
        return ['استحقاق الشهادة','تاريخ الدفع','استحقاق الى','استحقاق من ','حالة الدفع', 'نسبة الحضور', 'عدد الحضور', 'عدد الغياب','الدورة',' الشركة','الإيميل',' الجوال','رقم الهوية','اسم المتدرب'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->getStyle('A1:N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:N1')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setARGB('FFFFE599');

        foreach ($this->trainees as $key => $trainee) {
            $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
            $rowIndex = $key + 2;

            if ($attendanceNumeric >= 50 && $trainee['invoice_status']=='مدفوع') {
                $sheet->getCell("A$rowIndex")->setValue('يستحق');
                $sheet->getStyle("A$rowIndex")->getFont()->getColor()->setARGB('FF00FF00'); 
            } else {
                $sheet->getCell("A$rowIndex")->setValue('لا يستحق');
                $sheet->getStyle("A$rowIndex")->getFont()->getColor()->setARGB('FFFF0000'); 
            }


            $sheet->getCell("B$rowIndex")->setValue($trainee['paid_date']);

            $sheet->getCell("C$rowIndex")->setValue($trainee['invoice_to_date']);
            $sheet->getCell("D$rowIndex")->setValue($trainee['invoice_from_date']);

            $sheet->getCell("E$rowIndex")->setValue($trainee['invoice_status']);
            $sheet->getCell("F$rowIndex")->setValue($trainee['attendance_percentage']);
            $sheet->getCell("G$rowIndex")->setValue($trainee['present_count']);
            $sheet->getCell("H$rowIndex")->setValue($trainee['absent_count']);
            $sheet->getCell("I$rowIndex")->setValue($trainee['course_name']);

            $sheet->getCell("J$rowIndex")->setValue($trainee['company_name']);

            $sheet->getCell("K$rowIndex")->setValue($trainee['email']);
            $sheet->getCell("L$rowIndex")->setValue($trainee['phone']);
            $sheet->getCell("M$rowIndex")->setValue($trainee['identity_number']);
            $sheet->getCell("N$rowIndex")->setValue($trainee['trainee_name']);
            

            $sheet->getStyle("A$rowIndex:N$rowIndex")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
    }
}
