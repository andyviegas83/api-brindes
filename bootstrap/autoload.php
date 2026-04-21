<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\' => base_path('app'),
        'Bootstrap\\' => base_path('bootstrap'),
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
});
