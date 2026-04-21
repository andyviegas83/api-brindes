<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use InvalidArgumentException;

final class Container
{
    /**
     * @var array<string, Closure(self): mixed>
     */
    private array $bindings = [];

    /**
     * @var array<string, mixed>
     */
    private array $instances = [];

    public function singleton(string $id, Closure $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function set(string $id, mixed $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances) || array_key_exists($id, $this->bindings);
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->bindings)) {
            throw new InvalidArgumentException(sprintf('Service "%s" is not bound in the container.', $id));
        }

        $this->instances[$id] = ($this->bindings[$id])($this);

        return $this->instances[$id];
    }
}
