<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;
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

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->trainees);
    }

    public function headings(): array
    {
        return ['Trainee Name', 'Present Count', 'Absent Count','Attendance Percentage'];
    }

    /**
     * Apply styles to the spreadsheet.
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');

      
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setARGB('FFFFE599');
       
        $sheet->getStyle('A:D')->getAlignment()->setHorizontal('center');
    }
}
