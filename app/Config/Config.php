<?php

declare(strict_types=1);

namespace App\Config;

final class Config
{
    private static array $items = [];

    public static function load(string $configPath): void
    {
        $files = glob($configPath . DIRECTORY_SEPARATOR . '*.php') ?: [];

        foreach ($files as $file) {
            $key = basename($file, '.php');

            if ($key === '' || ctype_upper($key[0])) {
                continue;
            }

            self::$items[$key] = require $file;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}
