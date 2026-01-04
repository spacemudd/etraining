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
        
        // #region agent log
        file_put_contents('c:\\xampp\\htdocs\\new-project\\etraining\\backend\\.cursor\\debug.log', json_encode(['location'=>'NoonPaymentService.php:checkBnplOptions','message'=>'Before API call','data'=>['apiOperation'=>$paymentInfo['apiOperation'],'orderAmount'=>$paymentInfo['order']['amount'],'hasItems'=>isset($paymentInfo['order']['items']),'itemsCount'=>isset($paymentInfo['order']['items'])?count($paymentInfo['order']['items']):0,'hasCustomer'=>isset($paymentInfo['customer']),'hasBilling'=>isset($paymentInfo['billing']),'hasShipping'=>isset($paymentInfo['shipping']),'fullRequest'=>json_decode(json_encode($paymentInfo),true)],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run2','hypothesisId'=>'H'])."\n", FILE_APPEND);
        // #endregion
        
        $apiUrl = config("noon_payment." . ($centerId == 5676 ? 'jasarah' : 'jisr') . ".payment_api") . "order";
        $response = json_decode(CurlHelper::post($apiUrl, $paymentInfo, $this->getHeaders($centerId)));
        
        // #region agent log
        file_put_contents('c:\\xampp\\htdocs\\new-project\\etraining\\backend\\.cursor\\debug.log', json_encode(['location'=>'NoonPaymentService.php:checkBnplOptions','message'=>'API response','data'=>['resultCode'=>$response->resultCode??'N/A','message'=>$response->message??'N/A'],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run2','hypothesisId'=>'H'])."\n", FILE_APPEND);
        // #endregion
        
        return $response;
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
