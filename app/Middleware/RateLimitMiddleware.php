<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\TooManyRequestsException;
use App\Http\Request;
use App\Http\Response;

final class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $profile = 'public'
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        $config = (array) config(sprintf('api.%s_rate_limit', $this->profile), []);
        $maxAttempts = (int) ($config['max_attempts'] ?? 60);
        $decaySeconds = (int) ($config['decay_seconds'] ?? 60);
        $storagePath = (string) config('api.rate_limit_storage', base_path('storage/rate-limit'));

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
        }

        $key = sha1(implode('|', [
            $this->profile,
            $request->ip(),
            $request->path(),
        ]));

        $file = $storagePath . DIRECTORY_SEPARATOR . $key . '.json';
        $now = time();
        $windowStart = $now - $decaySeconds;
        $attempts = [];

        if (is_file($file)) {
            $decoded = json_decode((string) file_get_contents($file), true);

            if (is_array($decoded)) {
                $attempts = array_values(array_filter(
                    $decoded['attempts'] ?? [],
                    static fn (mixed $timestamp): bool => is_int($timestamp) && $timestamp >= $windowStart
                ));
            }
        }

        if (count($attempts) >= $maxAttempts) {
            throw new TooManyRequestsException('Rate limit exceeded.');
        }

        $attempts[] = $now;

        file_put_contents($file, json_encode(['attempts' => $attempts], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);

        $rateLimit = [
            'profile' => $this->profile,
            'limit' => $maxAttempts,
            'remaining' => max($maxAttempts - count($attempts), 0),
            'window_seconds' => $decaySeconds,
        ];

        $request = $request->withAttribute('rate_limit', $rateLimit);

        $response = $next($request);
        $payload = $response->payload();
        $meta = is_array($payload['meta'] ?? null) ? $payload['meta'] : [];
        $payload['meta'] = array_merge($meta, ['rate_limit' => $rateLimit]);

        return $response->withPayload($payload);
    }
}
