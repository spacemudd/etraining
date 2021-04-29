<?php

namespace App\Exports\Back;

use App\Models\Back\Trainee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomTraineeExport implements FromView, WithEvents, WithStyles
{

    public function __construct($trainees_type)
    {
        $this->trainees_type = $trainees_type;
    }

    public function registerEvents(): array
    {
        if (app()->getLocale() === 'ar') {
            return [
                AfterSheet::class    => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(true);
                },
            ];
        } else {
            return [
                AfterSheet::class    => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(false);
                },
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 12,
                ]
            ],
        ];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $q = Trainee::query();
        $archived = false;

        if($this->trainees_type == 'archived') {
            $trainees = $q->with('company')->onlyTrashed()->get()->toBase();

            $archived = true;

        } else {
            $trainees = Trainee::candidates()->get()->toBase();
        }

        return view('exports.customTrainees', [
            'trainees' => $trainees,
            'archived' => $archived,
        ]);
    }
}
