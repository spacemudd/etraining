<?php

namespace App\Services;

use CodeBugLab\NoonPayment\Helper\CurlHelper;
use Illuminate\Support\Facades\Log;

class NoonPaymentService
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function initiate($centerId, $paymentInfo)
    {
        $paymentInfo['apiOperation'] = "INITIATE";
        $paymentInfo['order']['channel'] = config("noon_payment.channel");
        $paymentInfo['order']['category'] = config("noon_payment.order_category");
        $paymentInfo['configuration']['tokenizeCc'] = false;
        $paymentInfo['customer'] = array_merge(
            $paymentInfo['customer'] ?? [],
            $this->getTabbyCustomerConfig()
        );
        $billingContact = $paymentInfo['billing']['contact'] ?? [];
        $shippingContact = $paymentInfo['shipping']['contact'] ?? $billingContact;

        $paymentInfo['billing'] = [
            'address' => $this->getTabbyBillingAddressConfig(),
            'contact' => $billingContact,
        ];
        $paymentInfo['shipping'] = [
            'address' => $this->getTabbyShippingAddressConfig(),
            'contact' => $shippingContact,
        ];

        if ($centerId === 5676) {
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.jasarah.return_url');
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";
            return $this->postInitiate(config("noon_payment.jasarah.payment_api") . "order", $paymentInfo, $centerId);
        } else {
            $paymentInfo['configuration']['returnUrl'] = (! empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.jisr.return_url');
            $paymentInfo['configuration']['paymentAction'] = (! empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";
            return $this->postInitiate(config("noon_payment.jisr.payment_api") . "order", $paymentInfo, $centerId);
        }
    }

    public function getOrder($orderId, $center_id)
    {
        return json_decode(CurlHelper::get(config("noon_payment." . ($center_id == 5676 ? 'jasarah' : 'jisr') . ".payment_api") . "order/" . $orderId, $this->getHeaders($center_id)));
    }

    public function getOrderByReference($reference, $center_id)
    {
        return json_decode(CurlHelper::get(config("noon_payment." . ($center_id == 5676 ? 'jasarah' : 'jisr') . ".payment_api") . "order/reference/" . $reference, $this->getHeaders($center_id)));
    }

    private function getHeaders($centerId)
    {
        return [
            "Content-type: application/json",
            "Authorization: Key_" . ($centerId == 5676 ? config("noon_payment.jasarah.mode") : config("noon_payment.jisr.mode")) . " " . ($centerId == 5676 ? config("noon_payment.jasarah.auth_key") : config("noon_payment.jisr.auth_key")),
        ];
    }

    private function getTabbyCustomerConfig(): array
    {
        return [
            'loyaltyLevel' => 50,
            'registeredSince' => '2022-01-14T14:15:22Z',
            'orderHistory' => [
                [
                    'purchaseTime' => '2022-07-14T11:15:22Z',
                    'amount' => 500,
                    'status' => 'complete',
                ],
                [
                    'purchaseTime' => '2022-08-01T14:25:12Z',
                    'amount' => 1500,
                    'status' => 'new',
                ],
                [
                    'purchaseTime' => '2022-07-24T10:15:10Z',
                    'amount' => 50,
                    'status' => 'processing',
                ],
                [
                    'purchaseTime' => '2022-06-24T14:35:22Z',
                    'amount' => 200,
                    'status' => 'refunded',
                ],
                [
                    'purchaseTime' => '2022-06-24T14:30:22Z',
                    'amount' => 600,
                    'status' => 'unknown',
                ],
                [
                    'purchaseTime' => '2022-06-24T14:32:22Z',
                    'amount' => 230,
                    'status' => 'canceled',
                ],
            ],
            'nvp' => [
                'key' => 'value',
            ],
        ];
    }

    private function getTabbyBillingAddressConfig(): array
    {
        return [
            'street' => 'King Abdulaziz Rd',
            'city' => 'Riyadh',
            'stateProvince' => 'Riyadh',
            'country' => 'SA',
        ];
    }

    private function getTabbyShippingAddressConfig(): array
    {
        return [
            'street' => 'King Abdulaziz',
            'city' => 'Riyadh',
            'stateProvince' => 'Riyadh',
            'country' => 'SA',
            'postalCode' => '12345',
        ];
    }

    private function postInitiate(string $url, array $paymentInfo, int $centerId): object
    {
        Log::info('Noon INITIATE payload summary', [
            'centerId' => $centerId,
            'reference' => $paymentInfo['order']['reference'] ?? null,
            'amount' => $paymentInfo['order']['amount'] ?? null,
            'currency' => $paymentInfo['order']['currency'] ?? null,
            'category' => $paymentInfo['order']['category'] ?? null,
            'channel' => $paymentInfo['order']['channel'] ?? null,
            'hasItems' => !empty($paymentInfo['order']['items']),
            'locale' => $paymentInfo['configuration']['locale'] ?? null,
            'hasCustomer' => !empty($paymentInfo['customer']),
            'billingContactKeys' => array_keys($paymentInfo['billing']['contact'] ?? []),
            'shippingContactKeys' => array_keys($paymentInfo['shipping']['contact'] ?? []),
        ]);

        $response = json_decode(CurlHelper::post($url, $paymentInfo, $this->getHeaders($centerId)));
        $paymentMethods = [];

        if (!empty($response->result->paymentOptions) && is_array($response->result->paymentOptions)) {
            foreach ($response->result->paymentOptions as $option) {
                $paymentMethods[] = $option->method ?? null;
            }
        }

        Log::info('Noon INITIATE response summary', [
            'centerId' => $centerId,
            'resultCode' => $response->resultCode ?? null,
            'message' => $response->message ?? null,
            'paymentMethods' => $paymentMethods,
        ]);

        return $response;
    }
}
