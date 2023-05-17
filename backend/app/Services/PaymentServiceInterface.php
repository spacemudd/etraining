<?php

namespace App\Services;

use App\Models\Back\Invoice;

interface PaymentServiceInterface
{
    public function createPaymentUrlForInvoice(Invoice $invoice): string;

    public function getOrder(string $order_id);

    public function isOrderSuccessful(string $order_id): bool;
}
