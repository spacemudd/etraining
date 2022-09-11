<?php

namespace App\Exports;

use App\Models\Back\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class PaidByCardReport implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $invoices = Invoice::where('payment_method', 1)
            ->get();

        return $invoices->toArray();
    }
}
