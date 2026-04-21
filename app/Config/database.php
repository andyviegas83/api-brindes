<?php

declare(strict_types=1);

use App\Config\Env;

$parseDatabaseUrl = static function (?string $databaseUrl): array {
    if ($databaseUrl === null || $databaseUrl === '') {
        return [];
    }

    $parts = parse_url($databaseUrl);

    if ($parts === false) {
        return [];
    }

    $query = [];
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
    }

    return [
        'driver' => match ($parts['scheme'] ?? null) {
            'postgres', 'postgresql', 'pgsql' => 'pgsql',
            'mysql' => 'mysql',
            default => 'pgsql',
        },
        'host' => $parts['host'] ?? '127.0.0.1',
        'port' => $parts['port'] ?? null,
        'database' => isset($parts['path']) ? ltrim($parts['path'], '/') : '',
        'username' => $parts['user'] ?? '',
        'password' => $parts['pass'] ?? '',
        'schema' => $query['schema'] ?? 'public',
        'charset' => 'utf8',
    ];
};

$appConnection = array_filter([
    'driver' => Env::get('DB_CONNECTION', 'pgsql'),
    'host' => Env::get('DB_HOST', '127.0.0.1'),
    'port' => (int) Env::get('DB_PORT', 5432),
    'database' => Env::get('DB_DATABASE', ''),
    'username' => Env::get('DB_USERNAME', ''),
    'password' => Env::get('DB_PASSWORD', ''),
    'charset' => Env::get('DB_CHARSET', 'utf8'),
    'schema' => Env::get('DB_SCHEMA', 'public'),
], static fn (mixed $value): bool => $value !== null);

$erpConnection = array_replace(
    $parseDatabaseUrl(Env::get('ERP_DATABASE_URL')),
    array_filter([
        'driver' => Env::get('ERP_DB_CONNECTION', null),
        'host' => Env::get('ERP_DB_HOST', null),
        'port' => Env::get('ERP_DB_PORT', null) !== null ? (int) Env::get('ERP_DB_PORT') : null,
        'database' => Env::get('ERP_DB_DATABASE', null),
        'username' => Env::get('ERP_DB_USERNAME', null),
        'password' => Env::get('ERP_DB_PASSWORD', null),
        'charset' => Env::get('ERP_DB_CHARSET', null),
        'schema' => Env::get('ERP_DB_SCHEMA', null),
    ], static fn (mixed $value): bool => $value !== null)
);

return [
    'default' => 'app',
    'erp_connection' => 'erp',
    'connections' => [
        'app' => $appConnection,
        'erp' => $erpConnection,
    ],
];
