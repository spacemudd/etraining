<?php

namespace App\Services;

use CodeBugLab\NoonPayment\Helper\CurlHelper;
use App\Helpers\EnvHelper;
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

    public function initiate($paymentInfo)
    {   




            $paymentInfo['apiOperation'] = "INITIATE";
            $paymentInfo['order']['channel'] = config("noon_payment.channel");
            $paymentInfo['order']['category'] = config("noon_payment.order_category");
            // Options for tokenize cc are (true - false)
            $paymentInfo['configuration']['tokenizeCc'] = (!empty($paymentInfo['configuration']['tokenizeCc'])) ? $paymentInfo['configuration']['tokenizeCc'] : "true";
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.jasarah.return_url');
            // Options for payment action are (AUTHORIZE - SALE)
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";
            return json_decode(CurlHelper::post(config("noon_payment.jasarah.payment_api") . "order", $paymentInfo, $this->getHeaders($centerId)));
        
        } else {
          
            $paymentInfo['apiOperation'] = "INITIATE";
            $paymentInfo['order']['channel'] = config("noon_payment.channel");
            $paymentInfo['order']['category'] = config("noon_payment.order_category");
            // Options for tokenize cc are (true - false)
            $paymentInfo['configuration']['tokenizeCc'] = (!empty($paymentInfo['configuration']['tokenizeCc'])) ? $paymentInfo['configuration']['tokenizeCc'] : "true";
            $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.jisr.return_url');
            // Options for payment action are (AUTHORIZE - SALE)
            $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";

        // if($accountName == 'Jasarah'){

        //     $paymentInfo['apiOperation'] = "INITIATE";
        //     $paymentInfo['order']['channel'] = config("noon_payment.jasarah.channel");
        //     $paymentInfo['order']['category'] = config("noon_payment.jasarah.order_category");
        //     // Options for tokenize cc are (true - false)
        //     $paymentInfo['configuration']['tokenizeCc'] = (!empty($paymentInfo['configuration']['tokenizeCc'])) ? $paymentInfo['configuration']['tokenizeCc'] : "true";
        //     $paymentInfo['configuration']['returnUrl'] = (!empty($paymentInfo['configuration']['returnUrl'])) ? $paymentInfo['configuration']['returnUrl'] : config('noon_payment.jasarah.return_url');
        //     // Options for payment action are (AUTHORIZE - SALE)
        //     $paymentInfo['configuration']['paymentAction'] = (!empty($paymentInfo['configuration']['paymentAction'])) ? $paymentInfo['configuration']['paymentAction'] : "SALE";

        //     return json_decode(CurlHelper::post(config("noon_payment.jasarah.payment_api") . "order", $paymentInfo, $this->getHeaders()));
        // }
        
        
    }

    public function getOrder($orderId, $center_id)
    {
        return json_decode(CurlHelper::get(config("noon_payment." . ($center_id == 3717 ? 'jasarah' : 'jisr') . ".payment_api") . "order/" . $orderId, $this->getHeaders($center_id)));
    }

    private function getHeaders()
    {

        return [
            "Content-type: application/json",
            "Authorization: Key_" . ($centerId == 3717 ? config("noon_payment.jasarah.mode") : config("noon_payment.jisr.mode")) . " " . ($centerId == 3717 ? config("noon_payment.jasarah.auth_key") : config("noon_payment.jisr.auth_key")),
        ];
        
    }
}