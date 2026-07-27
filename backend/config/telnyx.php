<?php

declare(strict_types=1);

return [
    'api_key' => env('TELNYX_API_KEY'),
    'whatsapp_from' => env('TELNYX_WHATSAPP_FROM'),
    'waba_id' => env('TELNYX_WABA_ID'),
    'phone_number_id' => env('TELNYX_PHONE_NUMBER_ID'),
    'messaging_profile_id' => env('TELNYX_MESSAGING_PROFILE_ID'),
    'webhook_public_key' => env('TELNYX_WEBHOOK_PUBLIC_KEY'),
    'status_callback_url' => env('TELNYX_STATUS_CALLBACK_URL'),
];
