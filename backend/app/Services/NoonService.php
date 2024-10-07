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

        $noonCredentials = $this->getNoonCredentials($invoice->company->center_id);


        // if testing, redirect to production site
        if (config('noon_payment.mode') === 'Test' && !Str::contains(auth()->user()->email ?? '', 'info@')) {
            return config('app.url');
        }

         $url = $this->noonPayment->initiate([
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
            'businessId' => $noonCredentials['businessId'],
            'appName' => $noonCredentials['appName'],
            'appKey' => $noonCredentials['appKey'],
        ],
    ]);

        dd($url);
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
        return $this->isPaymentSuccess($this->getOrder($order_id));
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

    public function getNoonCredentials(int $centerId): array
    {
        switch ($centerId) {
            case 5676: // مركز جسارة
                return [
                    'businessId' => config('noon_payment.business_id_jasarah'),
                    'appName' => config('noon_payment.app_name_jasarah'),
                    'appKey' => config('noon_payment.app_key_jasarah'),
                ];
            case 5675: // مركز جسر
            case 3: // مركز احترافية التدريب
                return [
                    'businessId' => config('noon_payment.business_id_jisr'),
                    'appName' => config('noon_payment.app_name_jisr'),
                    'appKey' => config('noon_payment.app_key_jisr'),
                ];
            default:
                throw new RuntimeException('Invalid center_id. Unable to set Noon credentials.');
        }
    }
}
