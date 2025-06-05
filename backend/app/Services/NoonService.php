<?php

namespace App\Services;

use App\Models\Back\Invoice;
use App\Services\NoonPaymentService;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * https://docs.noonpayments.com/start/introduction
 *
 */
class NoonService implements PaymentServiceInterface
{
    /**
     * Creates a payment url for a specific invoice.
     *
     * @param \App\Models\Back\Invoice $invoice
     * @return string URL of payment form
     * @throws \Exception
     */
    public function createPaymentUrlForInvoice(Invoice $invoice): string
    {
        $centerId = $invoice->trainee->company->center_id;

        $centerId = 5676; // As of 22-12-2024 - Change all payments to Jasarah.

        $webhookUrl = ($centerId == 5676 ? 'https://app.jasarah-ksa.com/noon' : 'https://app.jisr-ksa.com/noon');

        $url = NoonPaymentService::getInstance()->initiate(
            $centerId,
            [
            'order' => [
                'reference' => $invoice->id,
                'amount' => $invoice->grand_total,
                'currency' => 'SAR',
                'name' => Str::replace('  ', ' ', trim($invoice->trainee->name)),
                'description' => 'Training fees for period - '.$invoice->from_date.' - '.$invoice->to_date,
                // 'ipAddress' => request()->ip(),
            ],
            'billing' => [
                'contact' => [
                    'firstName' => Str::before($invoice->trainee->name, ' '),
                    'lastName' => Str::afterLast($invoice->trainee->name, ' '),
                    'phone' => $invoice->trainee->clean_phone,
                    //'email' => $invoice->trainee->email,
                ],
            ],
            'deviceFingerPrint' => [
                'sessionId' => request()->fingerprint(),
            ],
            'configuration' => [
                'locale' => 'ar',
                'webhookUrl' => $webhookUrl,
                'returnUrl' => 'https://app.jasarah-ksa.com/trainees/payment/card/charge-payment',
                // 'generateShortLink' => true, // TODO: When sharing the invoice with SMS.
            ]
        ]);


        if ($url->resultCode === 0) {
            return $url->result->checkoutData->postUrl;
        }

        throw new RuntimeException('Noon payment fatal error: '.$url->resultCode.' - '.$url->message);
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function getOrder($order_id, $center_id)
    {
        return NoonPaymentService::getInstance()->getOrder($order_id, $center_id);
    }

    public function isOrderSuccessful(string $order_id, $center_id): bool
    {
        $order = $this->getOrder($order_id, $center_id);
        return $this->isPaymentSuccess($order);
    }

    public function isPaymentSuccess($order): bool
    {
        return isset($order->result->transactions) &&
            is_array($order->result->transactions) &&
            $order->result->transactions[0]->type == "SALE" &&
            $order->result->transactions[0]->status == "SUCCESS";
    }
}
