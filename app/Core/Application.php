<?php

declare(strict_types=1);

namespace App\Core;

use App\Http\Request;
use App\Http\Response;

final class Application
{
    public function __construct(
        private readonly Logger $logger,
        private readonly Container $container,
        private readonly Router $router = new Router()
    ) {
    }

    public function bootstrapRoutes(string $routesFile): void
    {
        $router = $this->router;
        require $routesFile;
    }

    public function handle(Request $request): Response
    {
        $response = $this->router->dispatch($request);
        $payload = $response->payload();
        $meta = is_array($payload['meta'] ?? null) ? $payload['meta'] : [];

        $payload['meta'] = array_merge($meta, [
            'request_id' => $request->requestId(),
            'version' => (string) config('app.api_version', 'v1'),
        ]);

        if ($request->attribute('rate_limit') !== null) {
            $payload['meta']['rate_limit'] = $request->attribute('rate_limit');
        }

        $this->logger->info('API request handled', [
            'request_id' => $request->requestId(),
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->statusCode(),
            'ip' => $request->ip(),
        ]);

        return $response->withPayload($payload);
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function container(): Container
    {
        return $this->container;
    }
}
