<?php

declare(strict_types=1);

use App\Config\Env;

return [
    'header' => 'Authorization',
    'scheme' => 'Bearer',
    'tokens' => [
        'erp-crm-primary' => [
            'token' => Env::get('API_ADMIN_TOKEN', 'change-this-admin-token'),
            'abilities' => [
                'admin.read',
                'catalog.write',
                'content.write',
                'site.write',
            ],
        ],
        'erp-crm-readonly' => [
            'token' => Env::get('API_READONLY_TOKEN', 'change-this-readonly-token'),
            'abilities' => [
                'admin.read',
            ],
        ],
    ],
];
