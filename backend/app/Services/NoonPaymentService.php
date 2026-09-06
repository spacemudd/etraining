<?php

namespace App\Services;

use CodeBugLab\NoonPayment\Helper\CurlHelper;
use App\Helpers\EnvHelper;

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

        if ($this->isJasarahCenter($centerId)) {
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.jasarah.return_url');
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";
            return json_decode(CurlHelper::post(config("noon_payment.jasarah.payment_api") . "order", $paymentInfo, $this->getHeaders($centerId)));
        } else {
            $paymentInfo['configuration']['returnUrl'] = (! empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.jisr.return_url');
            $paymentInfo['configuration']['paymentAction'] = (! empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";
            return json_decode(CurlHelper::post(config("noon_payment.jisr.payment_api") . "order", $paymentInfo, $this->getHeaders($centerId)));
        }
    }

    public function getOrder($orderId, $center_id)
    {
        $account = $this->isJasarahCenter($center_id) ? 'jasarah' : 'jisr';

        return json_decode(CurlHelper::get(config("noon_payment." . $account . ".payment_api") . "order/" . $orderId, $this->getHeaders($center_id)));
    }

    public function getOrderByReference($reference, $center_id)
    {
        $account = $this->isJasarahCenter($center_id) ? 'jasarah' : 'jisr';

        return json_decode(CurlHelper::get(config("noon_payment." . $account . ".payment_api") . "order/reference/" . $reference, $this->getHeaders($center_id)));
    }

    private function getHeaders($centerId)
    {
        $account = $this->isJasarahCenter($centerId) ? 'jasarah' : 'jisr';

        return [
            "Content-type: application/json",
            "Authorization: Key_" . config("noon_payment.{$account}.mode") . " " . config("noon_payment.{$account}.auth_key"),
        ];
    }

    /**
     * Jasarah (5676) is the default merchant. Missing centerId must not fall through to Jisr.
     */
    private function isJasarahCenter($centerId): bool
    {
        if ($centerId === null || $centerId === '') {
            return true;
        }

        return (int) $centerId === 5676;
    }
}
