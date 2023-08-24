<?php

namespace App\Exports;

use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesSheetExport implements FromView, WithEvents
{
    use Exportable;

    public $startDate;

    public $endDate;

    public $companyId;

    /**
     * CourseSessionsAttendanceSummarySheetExport constructor.
     *
     * @param $startDate
     * @param $endDate
     * @param null $companyId
     */
    public function __construct($startDate, $endDate, $companyId=null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
        $invoices = Invoice::query()
            ->withoutGlobalScopes()
            ->where('deleted_at', null)
            ->with(['trainee', 'company', 'created_by'])
            ->whereBetween('from_date', [$this->startDate, $this->endDate]);

        if ($this->companyId) {
            $invoices->where('company_id', $this->companyId);
        }

        return view('exports.invoices', [
            'invoices' => $invoices->get(),
        ]);
    }
}
