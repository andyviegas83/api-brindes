<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\HttpException;
use App\Http\Request;
use App\Http\Response;

final class RequestValidationMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $maxBodyBytes = (int) config('api.max_body_bytes', 262144);
        $rawBodyLength = strlen($request->rawBody());

        if ($rawBodyLength > $maxBodyBytes) {
            throw new HttpException('Payload too large.', 413);
        }

        $accept = (string) $request->header('Accept', '');

        if ($accept !== '' && !str_contains($accept, 'application/json') && !str_contains($accept, '*/*')) {
            throw new HttpException('This API responds only with application/json.', 406);
        }

        return $next($request);
    }
}
