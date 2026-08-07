<?php

declare(strict_types=1);

return [
    'access_token' => env('WA_ACCESS_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Auto-injectable WhatsApp template tags
    |--------------------------------------------------------------------------
    |
    | Use these named placeholders in template bodies (e.g. {{trainee_name}}).
    | They are converted to Meta's numbered {{1}}, {{2}} format on submit, and
    | filled automatically when sending if a trainee is available.
    |
    */
    'template_auto_tags' => [
        'trainee_name' => [
            'label' => 'Trainee name',
            'example' => 'أحمد',
            'example_en' => 'Ahmed',
        ],
        'trainee_english_name' => [
            'label' => 'Trainee English name',
            'example' => 'Ahmed',
            'example_en' => 'Ahmed',
        ],
        'trainee_phone' => [
            'label' => 'Trainee phone',
            'example' => '0500000000',
            'example_en' => '0500000000',
        ],
        'trainee_identity' => [
            'label' => 'Trainee identity number',
            'example' => '1000000000',
            'example_en' => '1000000000',
        ],
        'company_name' => [
            'label' => 'Company name',
            'example' => 'شركة مثال',
            'example_en' => 'Example Company',
        ],
    ],
];
