<?php

namespace App\Exports;

use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\AttendanceReportRecord;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

class TraineeAccountSummary implements FromView, WithEvents
{
    public $trainee_id;

    public function __construct($trainee_id)
    {
        $this->trainee_id = $trainee_id;
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

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $transactions = AccountingLedgerBook::where('trainee_id', $this->trainee_id)
            ->get();

        return view('exports.trainee-account-statement', [
            'transactions' => $transactions,
        ]);
    }
}
