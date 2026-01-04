<?php

namespace App\Services;

use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
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

    /**
     * Get order(s) by reference (invoice ID)
     *
     * @param string $reference The invoice ID used as reference
     * @param int $center_id The center ID (5676 for Jasarah, 0 for Jisr)
     * @return mixed
     */
    public function getOrderByReference($reference, $center_id)
    {
        return NoonPaymentService::getInstance()->getOrderByReference($reference, $center_id);
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

    /**
     * Check available Tabby BNPL options for an invoice
     *
     * @param \App\Models\Back\Invoice $invoice
     * @return object
     * @throws \Exception
     */
    public function checkTabbyOptions(Invoice $invoice): object
    {
        $centerId = 5676; // Jasarah
        
        $trainee = $invoice->trainee;
        $orderHistory = $this->buildTabbyOrderHistory($trainee);
        $customerData = $this->buildTabbyCustomerData($invoice);
        
        $paymentInfo = [
            'order' => [
                'amount' => (string)$invoice->grand_total,
                'currency' => 'SAR',
                'channel' => config("noon_payment.channel"),
                'category' => config("noon_payment.order_category"),
                'name' => Str::replace('  ', ' ', trim($trainee->name)),
            ],
            'items' => $this->buildTabbyItems($invoice),
            'customer' => [
                'loyaltyLevel' => $trainee->invoices()->paid()->count(),
                'registeredSince' => $trainee->created_at->toIso8601String(),
                'orderHistory' => $orderHistory,
            ],
            'billing' => $customerData['billing'],
            'shipping' => $customerData['shipping'],
        ];
        
        return NoonPaymentService::getInstance()->checkBnplOptions($centerId, $paymentInfo);
    }

    /**
     * Create Tabby payment URL for an invoice
     *
     * @param \App\Models\Back\Invoice $invoice
     * @param string $productType
     * @param int $productId
     * @return string
     * @throws RuntimeException
     */
    public function createTabbyPaymentUrl(Invoice $invoice, $productType, $productId): string
    {
        $centerId = 5676;
        $webhookUrl = 'https://app.jasarah-ksa.com/noon';
        
        $trainee = $invoice->trainee;
        $orderHistory = $this->buildTabbyOrderHistory($trainee);
        $customerData = $this->buildTabbyCustomerData($invoice);
        
        $paymentInfo = [
            'order' => [
                'reference' => $invoice->id,
                'amount' => (string)$invoice->grand_total,
                'currency' => 'SAR',
                'channel' => config("noon_payment.channel"),
                'category' => config("noon_payment.order_category"),
                'name' => Str::replace('  ', ' ', trim($trainee->name)),
            ],
            'items' => $this->buildTabbyItems($invoice),
            'configuration' => [
                'locale' => 'ar',
                'webhookUrl' => $webhookUrl,
                'returnUrl' => 'https://app.jasarah-ksa.com/trainees/payment/tabby/charge-payment',
            ],
            'customer' => [
                'loyaltyLevel' => $trainee->invoices()->paid()->count(),
                'registeredSince' => $trainee->created_at->toIso8601String(),
                'orderHistory' => $orderHistory,
            ],
            'billing' => $customerData['billing'],
            'shipping' => $customerData['shipping'],
            'paymentData' => [
                'type' => 'TABBY',
                'data' => [
                    'productType' => $productType,
                    'productId' => $productId,
                ],
            ],
        ];
        
        $url = NoonPaymentService::getInstance()->initiateWithTabby($centerId, $paymentInfo);
        
        if ($url->resultCode === 0) {
            return $url->result->checkoutData->postUrl;
        }
        
        throw new RuntimeException('Tabby payment fatal error: '.$url->resultCode.' - '.$url->message);
    }

    /**
     * Build Tabby customer data (billing and shipping)
     *
     * @param \App\Models\Back\Invoice $invoice
     * @return array
     */
    private function buildTabbyCustomerData(Invoice $invoice): array
    {
        $trainee = $invoice->trainee;
        $addressParts = $this->parseNationalAddress($trainee->national_address);
        $cityName = optional($trainee->city)->name ?? 'Riyadh';
        $email = $trainee->email ?? 'customer@example.com';
        
        $firstName = Str::before($trainee->name, ' ');
        $lastName = Str::afterLast($trainee->name, ' ') ?: $firstName;
        
        $addressData = [
            'street' => $addressParts['street'],
            'city' => $cityName,
            'country' => 'SA',
            'postalCode' => $addressParts['postal_code'],
        ];
        
        $contactData = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $trainee->clean_phone,
            'email' => $email,
            'mobilePhone' => $trainee->clean_phone,
        ];
        
        return [
            'billing' => [
                'address' => $addressData,
                'contact' => $contactData,
            ],
            'shipping' => [
                'address' => $addressData,
                'contact' => $contactData,
            ],
        ];
    }

    /**
     * Build Tabby order history from trainee's paid invoices
     *
     * @param \App\Models\Back\Trainee $trainee
     * @return array
     */
    private function buildTabbyOrderHistory(Trainee $trainee): array
    {
        $invoices = $trainee->invoices()
            ->paid()
            ->orderBy('paid_at', 'desc')
            ->limit(10)
            ->get();
        
        return $invoices->map(function ($invoice) {
            return [
                'purchaseTime' => $invoice->paid_at->toIso8601String(),
                'amount' => (string)$invoice->grand_total,
                'status' => 'COMPLETE',
            ];
        })->toArray();
    }

    /**
     * Build Tabby items array from invoice items
     *
     * @param \App\Models\Back\Invoice $invoice
     * @return array
     */
    private function buildTabbyItems(Invoice $invoice): array
    {
        $items = $invoice->items;
        
        // If no items, create a default item
        if ($items->isEmpty()) {
            return [
                [
                    'name' => 'Training Fee',
                    'quantity' => 1,
                    'unitPrice' => (string)$invoice->grand_total,
                    'code' => 'service',
                    'nvp' => [],
                    'productSku' => 'default',
                ],
            ];
        }
        
        return $items->map(function ($item) {
            // Use Arabic name if available, otherwise English
            $itemName = $item->name_ar ?: $item->name_en ?: 'Training Fee';
            
            return [
                'name' => $itemName,
                'quantity' => $item->quantity ?? 1,
                'unitPrice' => (string)$item->grand_total,
                'code' => 'service',
                'nvp' => [],
                'productSku' => $item->id,
            ];
        })->toArray();
    }

    /**
     * Parse national address string into components
     *
     * @param string|null $address
     * @return array
     */
    private function parseNationalAddress(?string $address): array
    {
        if (!$address) {
            return [
                'street' => 'N/A',
                'postal_code' => '12345',
            ];
        }
        
        // Simple parsing - extract postal code if available (5 digits)
        $postalCode = '12345';
        if (preg_match('/\b\d{5}\b/', $address, $matches)) {
            $postalCode = $matches[0];
        }
        
        return [
            'street' => $address,
            'postal_code' => $postalCode,
        ];
    }
}
