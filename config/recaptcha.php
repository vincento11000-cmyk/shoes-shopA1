<?php

return [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'version' => 'v2', // v2 or v3
    'score_threshold' => 0.5, // For v3 only
];