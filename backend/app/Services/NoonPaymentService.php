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

        if ($centerId === 5676) {
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
        return json_decode(CurlHelper::get(config("noon_payment." . ($center_id == 5676 ? 'jasarah' : 'jisr') . ".payment_api") . "order/" . $orderId, $this->getHeaders($center_id)));
    }

    public function getOrderByReference($reference, $center_id)
    {
        return json_decode(CurlHelper::get(config("noon_payment." . ($center_id == 5676 ? 'jasarah' : 'jisr') . ".payment_api") . "order/reference/" . $reference, $this->getHeaders($center_id)));
    }

    /**
     * Check available BNPL options for Tabby
     *
     * @param int $centerId
     * @param array $paymentInfo
     * @return mixed
     */
    public function checkBnplOptions($centerId, $paymentInfo)
    {
        $paymentInfo['apiOperation'] = "CHECK_BNPL_OPTIONS";
        $paymentInfo['order']['channel'] = config("noon_payment.channel");
        $paymentInfo['order']['category'] = config("noon_payment.order_category");
        
        $apiUrl = config("noon_payment." . ($centerId == 5676 ? 'jasarah' : 'jisr') . ".payment_api") . "order";
        return json_decode(CurlHelper::post($apiUrl, $paymentInfo, $this->getHeaders($centerId)));
    }

    /**
     * Initiate order with Tabby BNPL
     *
     * @param int $centerId
     * @param array $paymentInfo
     * @return mixed
     */
    public function initiateWithTabby($centerId, $paymentInfo)
    {
        $paymentInfo['apiOperation'] = "INITIATE";
        $paymentInfo['order']['channel'] = config("noon_payment.channel");
        $paymentInfo['order']['category'] = config("noon_payment.order_category");
        $paymentInfo['configuration']['tokenizeCc'] = false;
        
        if ($centerId === 5676) {
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) 
                ? $paymentInfo['configuration']['returnUrl'] 
                : config('noon_payment.jasarah.return_url');
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) 
                ? $paymentInfo['configuration']['paymentAction'] 
                : "SALE";
            return json_decode(CurlHelper::post(config("noon_payment.jasarah.payment_api") . "order", $paymentInfo, $this->getHeaders($centerId)));
        } else {
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) 
                ? $paymentInfo['configuration']['returnUrl'] 
                : config('noon_payment.jisr.return_url');
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) 
                ? $paymentInfo['configuration']['paymentAction'] 
                : "SALE";
            return json_decode(CurlHelper::post(config("noon_payment.jisr.payment_api") . "order", $paymentInfo, $this->getHeaders($centerId)));
        }
    }

    private function getHeaders($centerId)
    {
        return [
            "Content-type: application/json",
            "Authorization: Key_" . ($centerId == 5676 ? config("noon_payment.jasarah.mode") : config("noon_payment.jisr.mode")) . " " . ($centerId == 5676 ? config("noon_payment.jasarah.auth_key") : config("noon_payment.jisr.auth_key")),
        ];
    }
}
