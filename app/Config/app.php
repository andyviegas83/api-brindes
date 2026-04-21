<?php

declare(strict_types=1);

use App\Config\Env;

return [
    'name' => Env::get('APP_NAME', 'ApiBrinde'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::get('APP_DEBUG', false),
    'url' => Env::get('APP_URL', 'http://localhost:8080'),
    'timezone' => Env::get('APP_TIMEZONE', 'America/Sao_Paulo'),
    'api_version' => Env::get('API_VERSION', 'v1'),
    'site_base_url' => Env::get('SITE_BASE_URL', ''),
    'erp_base_url' => Env::get('ERP_BASE_URL', ''),
    'log_path' => base_path((string) Env::get('LOG_PATH', 'logs/app.log')),
    'log_channel' => Env::get('LOG_CHANNEL', 'file'),
    'log_level' => Env::get('LOG_LEVEL', 'debug'),
];
