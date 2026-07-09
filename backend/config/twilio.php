<?php

declare(strict_types=1);

return [
  'account_sid' => env('TWILIO_ACCOUNT_SID'),
  'auth_token' => env('TWILIO_AUTH_TOKEN'),
  'verify_service_sid' => env('TWILIO_VERIFY_SERVICE_SID'),
  'verify_locale' => env('TWILIO_VERIFY_LOCALE', 'ar'),
  'messaging_service_sid' => env('TWILIO_MESSAGING_SERVICE_SID'),
  'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
  'webhook_base_url' => env('TWILIO_WEBHOOK_BASE_URL'),
  'status_callback_url' => env('TWILIO_STATUS_CALLBACK_URL'),
];
