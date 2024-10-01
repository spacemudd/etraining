<?php
return [
    /**
     * Register route for NoonPaymentController
     */
    "register_routes" => true,

    'business_id_jisarah' => env('NOON_PAYMENT_BUSINESS_ID_JASARAH'),
    'app_name_jasarah' => env('NOON_PAYMENT_APP_NAME_JASARAH'),
    'app_key_jasarah' => env('NOON_PAYMENT_APP_KEY_JASARAH'),
    'return_url_jasarah' => env('NOON_PAYMENT_RETURN_URL_JASARAH'),

    'business_id_jisr' => env('NOON_PAYMENT_BUSINESS_ID_JISR'),
    'app_name_jisr' => env('NOON_PAYMENT_APP_NAME_JISR'),
    'app_key_jisr' => env('NOON_PAYMENT_APP_KEY_JISR'),
    'return_url_jisr' => env('NOON_PAYMENT_RETURN_URL_JISR'),

    'mode' => env('NOON_PAYMENT_MODE', 'Test'),
    'order_category' => env('NOON_PAYMENT_ORDER_CATEGORY', 'pay'),
    'channel' => env('NOON_PAYMENT_CHANNEL', 'web'),
    'payment_api' => env('NOON_PAYMENT_PAYMENT_API'),
    'payment_api_test' => env('NOON_PAYMENT_PAYMENT_API_TEST'),

    /**
     *  Base64(BusinessIdentifier.ApplicationIdentifier:ApplicationKey)
     */
    "auth_key" => base64_encode(env('NOON_PAYMENT_BUSINESS_ID').".".env("NOON_PAYMENT_APP_NAME").":".env("NOON_PAYMENT_APP_KEY")),

    "token_identifier" => env('NOON_PAYMENT_TOKEN_IDENTIFIER'),
    "return_url" => env('NOON_PAYMENT_RETURN_URL'),
    "mode" => env('NOON_PAYMENT_MODE'),
    "order_category" => env('NOON_PAYMENT_ORDER_CATEGORY'),
    "channel" => env('NOON_PAYMENT_CHANNEL'),
    "payment_api" => env('NOON_PAYMENT_PAYMENT_API'),
];
