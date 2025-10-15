<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    |
    | إعدادات خاصة بإرسال الايميلات في النظام
    |
    */

    // الحد الأقصى لعدد المستلمين في الإيميل الواحد
    'max_recipients_per_email' => env('EMAIL_MAX_RECIPIENTS', 50),

    // تأخير بين الايميلات (بالثواني) لتجنب الـ rate limiting
    'delay_between_emails' => env('EMAIL_DELAY_SECONDS', 1),

    // تفعيل تسجيل تفاصيل الايميلات
    'enable_detailed_logging' => env('EMAIL_DETAILED_LOGGING', true),

    // تفعيل التحقق من صحة الايميلات قبل الإرسال
    'validate_emails' => env('EMAIL_VALIDATE_BEFORE_SEND', true),

    // إعادة المحاولة في حالة فشل الإرسال
    'retry_failed_emails' => env('EMAIL_RETRY_FAILED', true),
    'max_retry_attempts' => env('EMAIL_MAX_RETRY_ATTEMPTS', 3),

    // تقسيم الايميلات الكبيرة إلى دفعات
    'batch_large_emails' => env('EMAIL_BATCH_LARGE', true),
    'batch_size' => env('EMAIL_BATCH_SIZE', 10),
];
