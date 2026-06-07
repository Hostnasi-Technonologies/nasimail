<?php

declare(strict_types=1);

return [
    // Supported: api, mail
    'driver' => env('NASIMAIL_DRIVER', 'api'),

    'api' => [
        'base_url' => env('NASIMAIL_BASE_URL', 'https://nasimail.test'),
        'secret_key' => env('NASIMAIL_SECRET_KEY'),
        'timeout' => (int) env('NASIMAIL_TIMEOUT', 10),
        'retry_times' => (int) env('NASIMAIL_RETRY_TIMES', 3),
        'retry_sleep_ms' => (int) env('NASIMAIL_RETRY_SLEEP_MS', 400),
    ],

    // Uses Laravel Mail transport and app mail configuration.
    // Set this to "log" to force log mailer regardless of default.
    'mail' => [
        'mailer' => env('NASIMAIL_MAILER'),
    ],
];
