<?php

namespace App\Exports;

use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Invoice;
use App\Models\Back\TraineesComplaint;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintsSheetExport implements FromView, WithEvents
{
    use Exportable;

    public $companyId;

    /**
     * CourseSessionsAttendanceSummarySheetExport constructor.
     *
     * @param null $companyId
     */
    public function __construct($companyId=null)
    {
        $this->companyId = $companyId;
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
        $complaints = TraineesComplaint::query()
            ->withoutGlobalScopes()
            ->with(['trainee', 'company']);

        if ($this->companyId) {
            $complaints->where('company_id', $this->companyId);
        }

        return view('exports.complaints', [
            'trainees_complaints' => $complaints->get(),
        ]);
    }
}
