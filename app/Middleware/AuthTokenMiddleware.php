<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\UnauthorizedException;
use App\Http\Request;
use App\Http\Response;

final class AuthTokenMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $headerName = (string) config('auth.header', 'Authorization');
        $headerValue = trim((string) $request->header($headerName, ''));
        $scheme = (string) config('auth.scheme', 'Bearer');

        if ($headerValue === '' || !preg_match(sprintf('/^%s\s+(.+)$/i', preg_quote($scheme, '/')), $headerValue, $matches)) {
            throw new UnauthorizedException('Authentication token not provided.');
        }

        $providedToken = trim($matches[1]);
        $configuredTokens = (array) config('auth.tokens', []);

        foreach ($configuredTokens as $tokenName => $tokenConfig) {
            $expectedToken = trim((string) ($tokenConfig['token'] ?? ''));

            if ($expectedToken === '' || !hash_equals($expectedToken, $providedToken)) {
                continue;
            }

            $request = $request->withAttribute('auth_context', [
                'token_name' => $tokenName,
                'abilities' => array_values(array_unique((array) ($tokenConfig['abilities'] ?? []))),
            ]);

            return $next($request);
        }

        throw new UnauthorizedException('Invalid API token.');
    }
}
