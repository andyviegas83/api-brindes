<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\HttpException;
use App\Http\Request;
use App\Http\Response;

final class JsonOnlyMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $contentType = (string) $request->header('Content-Type', '');

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true) && $contentType !== '' && !str_contains($contentType, 'application/json')) {
            throw new HttpException('Only application/json payloads are accepted.', 415);
        }

        return $next($request);
    }
}
