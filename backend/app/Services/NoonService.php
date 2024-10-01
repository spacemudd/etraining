<?php

namespace App\Services;

use App\Models\Back\Invoice;
use CodeBugLab\NoonPayment\NoonPayment;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * https://docs.noonpayments.com/start/introduction
 *
 */
class NoonService implements PaymentServiceInterface
{
    private NoonPayment $noonPayment;

    public function __construct()
    {
        $this->noonPayment = NoonPayment::getInstance();
    }

    /**
     * Creates a payment url for a specific invoice.
     *
     * @param \App\Models\Back\Invoice $invoice
     * @return string URL of payment form
     * @throws \Exception
     */
    public function createPaymentUrlForInvoice(Invoice $invoice): string
    {
        $this->validateCompany($invoice);  
        $this->setNoonCredentials($invoice->company->center_id);

        // if testing, redirect to production site
        if (config('noon_payment.mode') === 'Test' && !Str::contains(auth()->user()->email, 'info@')) {
            return config('app.url');
        }

        $url = NoonPayment::getInstance()->initiate([
            'order' => [
                'reference' => $invoice->id,
                'amount' => $invoice->grand_total,
                'currency' => 'SAR',
                'name' => Str::replace('  ', ' ', trim($invoice->trainee->name)),
                'description' => 'Training fees for period - ' . $invoice->from_date . ' - ' . $invoice->to_date,
            ],
            'billing' => [
                'contact' => [
                    'firstName' => Str::before($invoice->trainee->name, ' '),
                    'lastName' => Str::afterLast($invoice->trainee->name, ' '),
                    'phone' => $invoice->trainee->clean_phone,
                ],
            ],
            'deviceFingerPrint' => [
                'sessionId' => request()->fingerprint(),
            ],
            'configuration' => [
                'locale' => 'ar',
                'webhookUrl' => route('webhooks.noon'),
                'returnUrl' => route('trainees.payment.card.charge'),
            ]
        ]);

        if ($url->resultCode === 0) {
            return $url->result->checkoutData->postUrl;
        }

        throw new RuntimeException('Noon payment fatal error: ' . $url->resultCode . ' - ' . $url->message);
    }

    public function getOrder(string $order_id)
    {
        return NoonPayment::getInstance()->getOrder($order_id);
    }

    public function isOrderSuccessful(string $order_id): bool
    {
        return $this->isPaymentSuccess($order_id);
    }

    public function isPaymentSuccess($order): bool
    {
        return isset($order->result->transactions) &&
            is_array($order->result->transactions) &&
            $order->result->transactions[0]->type == "SALE" &&
            $order->result->transactions[0]->status == "SUCCESS";
    }

    private function validateCompany(Invoice $invoice): void
    {
        $company = $invoice->company;

        if (!$company || !in_array($company->center_id, [3, 5675, 5676])) {
            throw new RuntimeException('Invalid center_id. This service is only available for مركز جسارة, مركز جسر, and مركز احترافية التدريب.');
        }
    }

    private function setNoonCredentials(int $centerId): void
    {
        // Use the integer IDs directly
        if ($centerId === 5676) { // مركز جسارة
            $this->noonPayment->setBusinessId(config('noon_payment.business_id_jasarah'));
            $this->noonPayment->setAppName(config('noon_payment.app_name_jasarah'));
            $this->noonPayment->setAppKey(config('noon_payment.app_key_jasarah'));
            $this->noonPayment->setReturnUrl(config('noon_payment.return_url_jasarah'));
        } elseif ($centerId === 5675 || $centerId === 3) { // مركز جسر or مركز احترافية التدريب
            $this->noonPayment->setBusinessId(config('noon_payment.business_id_jisr'));
            $this->noonPayment->setAppName(config('noon_payment.app_name_jisr'));
            $this->noonPayment->setAppKey(config('noon_payment.app_key_jisr'));
            $this->noonPayment->setReturnUrl(config('noon_payment.return_url_jisr'));
        } else {
            throw new RuntimeException('Invalid center_id. Unable to set Noon credentials.');
        }
    }
}
