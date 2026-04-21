<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query = [],
        private readonly array $body = [],
        private readonly string $rawBody = '',
        private readonly array $headers = [],
        private readonly array $server = [],
        private readonly array $attributes = []
    ) {
    }

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $rawBody = file_get_contents('php://input') ?: '';
        $decodedBody = json_decode($rawBody, true);

        return new self(
            $method,
            self::normalizePath($path),
            $_GET,
            is_array($decodedBody) ? $decodedBody : $_POST,
            $rawBody,
            function_exists('getallheaders') ? getallheaders() : [],
            $_SERVER,
            ['request_id' => bin2hex(random_bytes(8))]
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function body(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->body;
        }

        return $this->body[$key] ?? $default;
    }

    public function rawBody(): string
    {
        return $this->rawBody;
    }

    public function header(string $name, mixed $default = null): mixed
    {
        foreach ($this->headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }

        return $default;
    }

    public function server(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->server;
        }

        return $this->server[$key] ?? $default;
    }

    public function attribute(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->attributes;
        }

        return $this->attributes[$key] ?? $default;
    }

    public function routeParam(string $key, mixed $default = null): mixed
    {
        $params = $this->attribute('route_params', []);

        return is_array($params) ? ($params[$key] ?? $default) : $default;
    }

    public function requestId(): string
    {
        return (string) $this->attribute('request_id', '');
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '127.0.0.1');
    }

    public function userAgent(): string
    {
        return (string) $this->header('User-Agent', '');
    }

    public function withServerValue(string $key, mixed $value): self
    {
        $server = $this->server;
        $server[$key] = $value;

        return new self(
            $this->method,
            $this->path,
            $this->query,
            $this->body,
            $this->rawBody,
            $this->headers,
            $server,
            $this->attributes
        );
    }

    public function withAttribute(string $key, mixed $value): self
    {
        $attributes = $this->attributes;
        $attributes[$key] = $value;

        return new self(
            $this->method,
            $this->path,
            $this->query,
            $this->body,
            $this->rawBody,
            $this->headers,
            $this->server,
            $attributes
        );
    }

    public function withAttributes(array $attributes): self
    {
        return new self(
            $this->method,
            $this->path,
            $this->query,
            $this->body,
            $this->rawBody,
            $this->headers,
            $this->server,
            array_merge($this->attributes, $attributes)
        );
    }

    private static function normalizePath(string $path): string
    {
        $normalized = '/' . trim($path, '/');

        return $normalized === '//' ? '/' : $normalized;
    }
}
