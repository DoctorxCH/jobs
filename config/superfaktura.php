<?php

return [
    'base_url' => env('SUPERFAKTURA_BASE_URL', 'https://api.superfaktura.sk'),
    'email' => env('SUPERFAKTURA_EMAIL'),
    'api_key' => env('SUPERFAKTURA_API_KEY'),
    'company_id' => env('SUPERFAKTURA_COMPANY_ID'),
    'numbering' => [
        'series_id' => env('SUPERFAKTURA_SERIES_ID'),
    ],
];
