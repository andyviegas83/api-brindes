<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Http\Response;

final class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $statusCode = 200,
        array $meta = []
    ): Response {
        return new Response([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'error' => null,
            'meta' => self::baseMeta($meta),
        ], $statusCode);
    }

    public static function error(
        string $message = 'Request could not be processed.',
        int $statusCode = 400,
        mixed $data = null,
        ?string $code = null,
        array $meta = []
    ): Response {
        return new Response([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'error' => [
                'code' => $code ?? self::errorCodeFromStatus($statusCode),
                'status' => $statusCode,
            ],
            'meta' => self::baseMeta($meta),
        ], $statusCode);
    }

    private static function baseMeta(array $meta = []): array
    {
        return array_merge([
            'timestamp' => date(DATE_ATOM),
            'version' => (string) config('app.api_version', 'v1'),
        ], $meta);
    }

    private static function errorCodeFromStatus(int $statusCode): string
    {
        return match ($statusCode) {
            401 => 'unauthorized',
            403 => 'forbidden',
            404 => 'not_found',
            405 => 'method_not_allowed',
            415 => 'unsupported_media_type',
            429 => 'rate_limit_exceeded',
            default => $statusCode >= 500 ? 'internal_error' : 'request_error',
        };
    }
}
