<?php

namespace App\Exports;

use App\Models\Back\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class ExportArchivedCompanies implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Company::onlyTrashed()
            ->get(['name_ar'])
            ->map(function ($company) {
                return [
                    'name_ar' => $company->name_ar,
                ];
            });
    }

    /**
     */
    public function headings(): array
    {
        return [
            'إسم الشركة'
        ];
    }

    /**
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], 
            'A' => ['alignment' => ['horizontal' => 'center']], 
        ];
    }

    /**
     * ضبط عرض الأعمدة
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30, 
        ];
    }

   
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D3D3D3'], 
                    ],
                    'font' => [
                        'color' => ['rgb' => '000000'], 
                        'bold' => true,
                        'size' => 12
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('A1:A' . $event->sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }
}
