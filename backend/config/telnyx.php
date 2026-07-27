<?php

declare(strict_types=1);

return [
    'api_key' => env('TELNYX_API_KEY'),
    'whatsapp_from' => env('TELNYX_WHATSAPP_FROM', '+966507688199'),
    'waba_id' => env('TELNYX_WABA_ID', '1059951236894448'),
    'phone_number_id' => env('TELNYX_PHONE_NUMBER_ID', '1129747513565105'),
    'webhook_public_key' => env('TELNYX_WEBHOOK_PUBLIC_KEY'),
    'status_callback_url' => env('TELNYX_STATUS_CALLBACK_URL'),
];
