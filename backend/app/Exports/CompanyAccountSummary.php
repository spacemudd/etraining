<?php

namespace App\Exports;

use App\Models\Back\AccountingLedgerBook;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CompanyAccountSummary implements FromView, WithEvents
{
    public $company_id;

    public function __construct($company_id)
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

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $transactions = AccountingLedgerBook::where('company_id', $this->company_id)
            ->get();

        return view('exports.company-account-statement', [
            'transactions' => $transactions,
        ]);
    }
}
