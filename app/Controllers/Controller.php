<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Response;

abstract class Controller
{
    protected function success(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $statusCode = 200,
        array $meta = []
    ): Response {
        return ApiResponse::success($data, $message, $statusCode, $meta);
    }

    protected function error(
        string $message = 'Request could not be processed.',
        int $statusCode = 400,
        mixed $data = null,
        ?string $code = null,
        array $meta = []
    ): Response {
        return ApiResponse::error($message, $statusCode, $data, $code, $meta);
    }
}
