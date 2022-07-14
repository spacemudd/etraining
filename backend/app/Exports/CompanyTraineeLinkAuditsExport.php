<?php

namespace App\Exports;

use App\Models\Back\Company;
use App\Models\Back\CompanyTraineeLinkAudit;
use App\Models\Back\TraineeCompanyMovement;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyTraineeLinkAuditsExport implements FromView, WithEvents, WithStyles, WithColumnWidths
{
    public $request;
    public $comapny_id;

    public function __construct($request, $company_id)
    {
        $this->request = $request;
        $this->company_id = $company_id;
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

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 22,
            'G' => 22,

        ];
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $dates = [
            Timezone::convertFromLocal($this->request->from_date)->startOfDay(),
            Timezone::convertFromLocal($this->request->to_date)->endOfDay(),
        ];

        $records = CompanyTraineeLinkAudit::whereBetween('created_at', $dates)
            ->where('company_id', $this->company_id)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])->groupBy('trainee_id');

        return view('exports.company-trainee-activity-log', [
            'activity_logs' => $records->get(),
            'company' => Company::findOrFail($this->company_id),
        ]);
    }
}
