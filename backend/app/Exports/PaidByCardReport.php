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

        return $invoices->map(function($item, $key) {
            return [
                'name' => $item->trainee->name,
                'company' => $item->company->name_ar,
                'date' => $item->from_date,
                'payment_method' => $item->payment_method,
                'grand_total' => $item->grand_total,
                'paid_at' => $item->paid_at,
            ];
        })->toArray();

        return $invoices->toArray();
    }
}
