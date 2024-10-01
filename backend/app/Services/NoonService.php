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
        $this->setNoonCredentials();


        // if testing, redirect to production site
        if (config('noon_payment.mode') === 'Test' && ! Str::contains(auth()->user()->email, 'info@')) {
            return config('app.url');
        }

        $url = NoonPayment::getInstance()->initiate([
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
                'webhookUrl' => route('webhooks.noon'),
                'returnUrl' => route('trainees.payment.card.charge'),
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
    public function getOrder($order_id)
    {
        return NoonPayment::getInstance()->getOrder($order_id);
    }

    public function isOrderSuccessful(string $order_id): bool
    {
        $order = $this->getOrder($order_id);
        return $this->isPaymentSuccess($order);
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
        $trainee = $invoice->trainee;
        $company = $trainee->company;

        if (!$company || !in_array($company->center_id, ['مركز جسارة', 'مركز جسر'])) {
            throw new RuntimeException('Invalid center_id. This service is only available for مركز جسارة and مركز جسر.');
        }
    }


    private function setNoonCredentials(string $centerId): void
    {
        if ($centerId === 'مركز جسارة') {
            NoonPayment::getInstance()->setBusinessId(config('noon_payment.business_id_jasarah'));
            NoonPayment::getInstance()->setAppName(config('noon_payment.app_name_jasarah'));
            NoonPayment::getInstance()->setAppKey(config('noon_payment.app_key_jasarah'));
            NoonPayment::getInstance()->setReturnUrl(config('noon_payment.return_url_jasarah'));
        } elseif ($centerId === 'مركز جسر') {
            NoonPayment::getInstance()->setBusinessId(config('noon_payment.business_id_jisr'));
            NoonPayment::getInstance()->setAppName(config('noon_payment.app_name_jisr'));
            NoonPayment::getInstance()->setAppKey(config('noon_payment.app_key_jisr'));
            NoonPayment::getInstance()->setReturnUrl(config('noon_payment.return_url_jisr'));
        } else {
            throw new RuntimeException('Invalid center_id. Unable to set Noon credentials.');
        }
    }

}
