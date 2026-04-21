<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Middleware\MiddlewareInterface;

final class Router
{
    /**
     * @var array<int, array{method: string, path: string, action: callable|array, middleware: array<int, string|MiddlewareInterface>}>
     */
    private array $routes = [];

    public function get(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function patch(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $action, $middleware);
    }

    public function delete(string $path, callable|array $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    public function dispatch(Request $request): Response
    {
        $allowedMethods = [];

        foreach ($this->routes as $route) {
            $routeParams = $this->matchPath($route['path'], $request->path());

            if ($routeParams === null) {
                continue;
            }

            if ($route['method'] !== $request->method()) {
                $allowedMethods[] = $route['method'];
                continue;
            }

            $request = $request->withAttributes([
                'route_params' => $routeParams,
                'route_path' => $route['path'],
                'route_method' => $route['method'],
            ]);

            return $this->runRoute($request, $route['action'], $route['middleware']);
        }

        if ($allowedMethods !== []) {
            throw new MethodNotAllowedException(array_values(array_unique($allowedMethods)));
        }

        throw new NotFoundException('Route not found.');
    }

    private function addRoute(string $method, string $path, callable|array $action, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $this->normalizePath($path),
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    private function runRoute(Request $request, callable|array $action, array $middleware): Response
    {
        $container = $request->server('app')?->container();
        $runner = array_reduce(
            array_reverse($middleware),
            static function (callable $next, string|MiddlewareInterface $layer) use ($container): callable {
                return static function (Request $request) use ($layer, $next, $container): Response {
                    $middleware = $layer;

                    if (is_string($layer)) {
                        $middleware = $container !== null && $container->has($layer)
                            ? $container->get($layer)
                            : new $layer();
                    }

                    return $middleware->handle($request, $next);
                };
            },
            fn (Request $request): Response => $this->resolveAction($action, $request)
        );

        return $runner($request);
    }

    private function resolveAction(callable|array $action, Request $request): Response
    {
        if (is_callable($action)) {
            return $action($request);
        }

        [$controller, $method] = $action;
        $container = $request->server('app')?->container();
        $instance = $container !== null && $container->has($controller)
            ? $container->get($controller)
            : new $controller();

        return $instance->{$method}($request);
    }

    private function normalizePath(string $path): string
    {
        $normalized = '/' . trim($path, '/');

        return $normalized === '//' ? '/' : $normalized;
    }

    private function matchPath(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z0-9_]+)\}/',
            static fn (array $matches): string => sprintf('(?P<%s>[^/]+)', $matches[1]),
            $routePath
        );

        $regex = sprintf('#^%s$#', $pattern);

        if (!preg_match($regex, $requestPath, $matches)) {
            return null;
        }

        $params = [];

        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
