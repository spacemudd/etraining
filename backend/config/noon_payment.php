<?php
return [
    /**
     * Register route for NoonPaymentController
     */
    "register_routes" => true,

    // 'business_id_jisarah' => env('NOON_PAYMENT_BUSINESS_ID_JASARAH'),
    // 'app_name_jisarah' => env('NOON_PAYMENT_APP_NAME_JASARAH'),
    // 'app_key_jisarah' => env('NOON_PAYMENT_APP_KEY_JASARAH'),
    // 'return_url_jisarah' => env('NOON_PAYMENT_RETURN_URL_JASARAH'),

    // 'business_id_jisr' => env('NOON_PAYMENT_BUSINESS_ID_JISR'),
    // 'app_name_jisr' => env('NOON_PAYMENT_APP_NAME_JISR'),
    // 'app_key_jisr' => env('NOON_PAYMENT_APP_KEY_JISR'),
    // 'return_url_jisr' => env('NOON_PAYMENT_RETURN_URL_JISR'),

    // 'mode' => env('NOON_PAYMENT_MODE', 'Live'),
    // 'order_category' => env('NOON_PAYMENT_ORDER_CATEGORY', 'pay'),
    // 'channel' => env('NOON_PAYMENT_CHANNEL', 'web'),
    // 'payment_api' => env('NOON_PAYMENT_PAYMENT_API'),
    // 'payment_api_test' => env('NOON_PAYMENT_PAYMENT_API_TEST'),

    // /**
    //  *  Base64(BusinessIdentifier.ApplicationIdentifier:ApplicationKey)
    //  */
    // "auth_key" => base64_encode(env('NOON_PAYMENT_BUSINESS_ID_JISR').".".env("NOON_PAYMENT_APP_NAME_JISR").":".env("NOON_PAYMENT_APP_KEY_JISR")),

    // "token_identifier" => env('NOON_PAYMENT_TOKEN_IDENTIFIER'),
    // "return_url" => env('NOON_PAYMENT_RETURN_URL'),


   // "business_id" => "jisr",
    // "app_name" => "jisr_prod",
    // "app_key" => "fd9d4315079641f1b873022a77e822f9",
    // "auth_key" => "amFzYXJhaC5qYXNhcmFoX2xhcmF2ZWw6MDk1YzJkM2ExNjQ1NDE0YzgyY2QyYmE2Njk4YWY0YTA=",
    // "token_identifier" => "",
    // "return_url" => "https://app.jisr-ksa.com/noon",
    // "mode" => "live",
    // "order_category" => "pay",
    // "channel" => "web",
    // "payment_api" => "https://api.noonpayments.com/payment/v1/",

    // Array of configurations for different businesses
    "jisr" => [
        "business_id" => env('JISR_NOON_PAYMENT_BUSINESS_ID', 'jisr'),
        "app_name" => env('JISR_NOON_PAYMENT_APP_NAME', 'jisr_prod'),
        "app_key" => env('JISR_NOON_PAYMENT_APP_KEY', 'fd9d4315079641f1b873022a77e822f9'),
        "token_identifier" => env('JISR_NOON_PAYMENT_TOKEN_IDENTIFIER', ''),
        "return_url" => env('JISR_NOON_PAYMENT_RETURN_URL', 'https://app.jisr-ksa.com/noon'),
        "mode" => env('JISR_NOON_PAYMENT_MODE', 'Live'),
        "order_category" => env('JISR_NOON_PAYMENT_ORDER_CATEGORY', 'pay'),
        "channel" => env('JISR_NOON_PAYMENT_CHANNEL', 'web'),
        "payment_api" => env('JISR_NOON_PAYMENT_PAYMENT_API', 'https://api.noonpayments.com/payment/v1/'),
    ],

    "jasarah" => [
        "business_id" => env('JASARAH_NOON_PAYMENT_BUSINESS_ID'),
        "app_name" => env('JASARAH_NOON_PAYMENT_APP_NAME'),
        "app_key" => env('JASARAH_NOON_PAYMENT_APP_KEY'),
        "token_identifier" => env('JASARAH_NOON_PAYMENT_TOKEN_IDENTIFIER', ''),
        "return_url" => env('JASARAH_NOON_PAYMENT_RETURN_URL'),
        "mode" => env('JASARAH_NOON_PAYMENT_MODE', 'Live'),
        "order_category" => env('JASARAH_NOON_PAYMENT_ORDER_CATEGORY'),
        "channel" => env('JASARAH_NOON_PAYMENT_CHANNEL', 'web'),
        "payment_api" => env('JASARAH_NOON_PAYMENT_PAYMENT_API'),
    ],

    // Function to switch between the businesses

];





