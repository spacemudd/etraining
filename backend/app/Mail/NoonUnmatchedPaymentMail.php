<?php

declare(strict_types=1);

namespace App\Mail;

use App\Services\CompanyMigrationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoonUnmatchedPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, mixed> */
    public array $paymentContext;

    /**
     * @param array<string, mixed> $paymentContext
     */
    public function __construct(array $paymentContext)
    {
        $this->paymentContext = $paymentContext;

        CompanyMigrationHelper::setMailgunConfigStatic();
    }

    public function build(): self
    {
        $orderId = $this->paymentContext['noon_order_id'] ?? $this->paymentContext['order_id'] ?? 'unknown';

        return $this
            ->subject('🚨 دفع Noon بدون فاتورة مطابقة — #' . $orderId)
            ->view('emails.noon-unmatched-payment', [
                'payment' => $this->paymentContext,
            ]);
    }
}
