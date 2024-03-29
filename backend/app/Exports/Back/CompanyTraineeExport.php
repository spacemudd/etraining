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

class CompanyTraineeExport implements FromView, WithEvents, WithStyles
{
    public $company_id;

    public function __construct(string $company_id)
    {
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

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $q = Trainee::query()->where('company_id', $this->company_id)->withTrashed();

        $trainees = $q->with('company')->get()->toBase();

        return view('exports.trainees', [
            'trainees' => $trainees,
        ]);
    }

    //public function headings(): array
    //{
    //    return [
    //        'id',
    //        'team_id',
    //        'user_id',
    //        'name',
    //        'email',
    //        'identity_number',
    //        'phone',
    //        'phone_additional',
    //        'birthday',
    //        'educational_level_id',
    //        'city_id',
    //        'marital_status_id',
    //        'children_count',
    //        'company_id',
    //        'deleted_at',
    //        'created_at',
    //        'updated_at',
    //        'instructor_id',
    //        'status',
    //        'approved_by_id',
    //        'approved_at',
    //        'deleted_remark',
    //        'skip_uploading_id',
    //    ];
    //}
}
