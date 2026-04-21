<?php

declare(strict_types=1);

namespace App\Core;

use App\Helpers\ApiResponse;
use App\Exceptions\HttpException;
use Throwable;

final class ErrorHandler
{
    public static function register(Logger $logger, bool $debug): void
    {
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(static function (Throwable $exception) use ($logger, $debug): void {
            $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;
            $requestId = bin2hex(random_bytes(8));

            $logger->error('Unhandled exception', [
                'request_id' => $requestId,
                'type' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            $meta = ['request_id' => $requestId];

            if ($debug) {
                $meta['exception'] = [
                    'type' => $exception::class,
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ];
            }

            $response = ApiResponse::error(
                $statusCode >= 500 ? 'Internal server error.' : $exception->getMessage(),
                $statusCode,
                null,
                null,
                $meta
            );

            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode($response->payload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        });
    }
}
