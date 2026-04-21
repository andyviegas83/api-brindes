<?php

declare(strict_types=1);

use App\Config\Env;

return [
    'max_body_bytes' => (int) Env::get('API_MAX_BODY_BYTES', 262144),
    'public_rate_limit' => [
        'max_attempts' => (int) Env::get('API_PUBLIC_RATE_LIMIT_MAX', 120),
        'decay_seconds' => (int) Env::get('API_PUBLIC_RATE_LIMIT_WINDOW', 60),
    ],
    'protected_rate_limit' => [
        'max_attempts' => (int) Env::get('API_PROTECTED_RATE_LIMIT_MAX', 300),
        'decay_seconds' => (int) Env::get('API_PROTECTED_RATE_LIMIT_WINDOW', 60),
    ],
    'rate_limit_storage' => base_path((string) Env::get('API_RATE_LIMIT_STORAGE', 'storage/rate-limit')),
];
