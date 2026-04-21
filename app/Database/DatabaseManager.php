<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use RuntimeException;

final class DatabaseManager
{
    /**
     * @var array<string, PDO>
     */
    private array $connections = [];

    public function __construct(
        private readonly array $config,
        private readonly ConnectionFactory $factory
    ) {
    }

    public function connection(?string $name = null): PDO
    {
        $connectionName = $name ?? ($this->config['default'] ?? 'app');

        if (isset($this->connections[$connectionName])) {
            return $this->connections[$connectionName];
        }

        $connectionConfig = $this->config['connections'][$connectionName] ?? null;

        if (!is_array($connectionConfig)) {
            throw new RuntimeException(sprintf('Database connection "%s" is not configured.', $connectionName));
        }

        return $this->connections[$connectionName] = $this->factory->make($connectionConfig);
    }
}
