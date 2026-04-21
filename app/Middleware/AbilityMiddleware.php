<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\ForbiddenException;
use App\Http\Request;
use App\Http\Response;

final class AbilityMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $requiredAbility
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        $authContext = $request->attribute('auth_context', []);
        $abilities = is_array($authContext) ? ($authContext['abilities'] ?? []) : [];

        if (!is_array($abilities) || !in_array($this->requiredAbility, $abilities, true)) {
            throw new ForbiddenException('Token does not have the required ability.');
        }

        return $next($request);
    }
}
