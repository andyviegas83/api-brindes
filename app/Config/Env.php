<?php

declare(strict_types=1);

namespace App\Config;

final class Env
{
    public static function load(string $filePath): void
    {
        if (!is_file($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            [$name, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
            $name = trim($name);
            $value = trim($value);

            if ($name === '' || array_key_exists($name, $_ENV)) {
                continue;
            }

            $normalized = self::normalizeValue($value);

            $_ENV[$name] = $normalized;
            $_SERVER[$name] = $normalized;
            putenv(sprintf('%s=%s', $name, (string) $normalized));
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        $value = getenv($key);

        return $value === false ? $default : $value;
    }

    private static function normalizeValue(string $value): mixed
    {
        $value = trim($value, "\"'");

        return match (strtolower($value)) {
            'true' => true,
            'false' => false,
            'null' => null,
            'empty' => '',
            default => $value,
        };
    }
}
