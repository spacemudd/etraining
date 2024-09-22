<?php

namespace App\Services;

use App\Models\Back\Invoice;
use App\Models\Back\InvoiceItem;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;

class InvoiceService
{
    /**
     * @param string $invoice_id
     * @param $amount
     * @return \App\Models\Back\Invoice|\App\Models\Back\Invoice[]|\LaravelIdea\Helper\App\Models\Back\_IH_Invoice_C|null
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function changeInvoiceCost(string $invoice_id, $amount): Invoice
    {
        $invoice = Invoice::find($invoice_id);
        $grand_total = Money::of($amount, 'SAR', new CustomContext(2), RoundingMode::HALF_UP);
        $sub_total = $grand_total->multipliedBy(1 / (1 + InvoiceItem::DEFAULT_TAX), RoundingMode::HALF_UP);
        $tax = $grand_total->minus($sub_total);

        $invoice->update([
            'grand_total' => $grand_total->getAmount()->toFloat(),
            'sub_total' => $sub_total->getAmount()->toFloat(),
            'tax' => $tax->getAmount()->toFloat(),
        ]);

        return $invoice;
    }
}
