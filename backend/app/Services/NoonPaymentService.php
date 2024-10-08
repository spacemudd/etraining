<?php

namespace App\Services;

use CodeBugLab\NoonPayment\Helper\CurlHelper;

class NoonPaymentService
{
    private static $instance = null;

    private function __construct()
    {
        //
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new NoonPaymentService();
        }
        return self::$instance;
    }

    public function initiate($centerId, $paymentInfo)
    {
        if($centerId == 3717){

            $paymentInfo['apiOperation'] = "INITIATE";
            $paymentInfo['order']['channel'] = config("noon_payment.channel");
            $paymentInfo['order']['category'] = config("noon_payment.order_category");
            // Options for tokenize cc are (true - false)
            $paymentInfo['configuration']['tokenizeCc'] = (!empty($paymentInfo['configuration']['tokenizeCc'])) ? $paymentInfo['configuration']['tokenizeCc'] : "true";
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.return_url');
            // Options for payment action are (AUTHORIZE - SALE)
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";
            return json_decode(CurlHelper::post(config("noon_payment.jasarah.payment_api") . "order", $paymentInfo, $this->getHeaders($centerId)));
        
        } else {
          
            $paymentInfo['apiOperation'] = "INITIATE";
            $paymentInfo['order']['channel'] = config("noon_payment.channel");
            $paymentInfo['order']['category'] = config("noon_payment.order_category");
            // Options for tokenize cc are (true - false)
            $paymentInfo['configuration']['tokenizeCc'] = (!empty($paymentInfo['configuration']['tokenizeCc'])) ? $paymentInfo['configuration']['tokenizeCc'] : "true";
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.return_url');
            // Options for payment action are (AUTHORIZE - SALE)
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";

            return json_decode(CurlHelper::post(config("noon_payment.jisr.payment_api") . "order", $paymentInfo, $this->getHeaders($centerId)));
        
        }
    }

    public function getOrder($centerId,$orderId)
    {
        if($centerId == 3717){
       
            return json_decode(CurlHelper::get(config("noon_payment.jasarah.payment_api") . "order/" . $orderId, $this->getHeaders($centerId)));
        }else{
            return json_decode(CurlHelper::get(config("noon_payment.jisr.payment_api") . "order/" . $orderId, $this->getHeaders($centerId)));

        }
    }

    private function getHeaders($centerId)
    {

        if($centerId == 3717) {
           
                return [
                    "Content-type: application/json",
                    "Authorization: Key_" . config("noon_payment.jasarah.mode") . " " . config("noon_payment.jasarah.auth_key"),
                ];
        }else{
                return [
                    "Content-type: application/json",
                    "Authorization: Key_" . config("noon_payment.jisr.mode") . " " . config("noon_payment.jisr.auth_key"),
                ];
        }
        
    }
}
