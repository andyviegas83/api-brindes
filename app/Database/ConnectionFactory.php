<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

final class ConnectionFactory
{
    public function make(array $config): PDO
    {
        $driver = $config['driver'] ?? 'pgsql';

        if ($driver === 'pgsql' && !extension_loaded('pdo_pgsql')) {
            throw new RuntimeException('The pdo_pgsql extension is required to connect to the ERP PostgreSQL database.');
        }

        if ($driver === 'mysql' && !extension_loaded('pdo_mysql')) {
            throw new RuntimeException('The pdo_mysql extension is required to connect to the configured MySQL database.');
        }

        $dsn = match ($driver) {
            'pgsql' => $this->makePgsqlDsn($config),
            'mysql' => $this->makeMysqlDsn($config),
            default => throw new RuntimeException(sprintf('Database driver "%s" is not supported.', $driver)),
        };

        try {
            $pdo = new PDO(
                $dsn,
                (string) ($config['username'] ?? ''),
                (string) ($config['password'] ?? ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            throw new RuntimeException('Database connection could not be established.', 0, $exception);
        }

        if ($driver === 'pgsql' && !empty($config['schema'])) {
            $pdo->exec(sprintf('SET search_path TO %s', $this->quoteIdentifier((string) $config['schema'])));
        }

        return $pdo;
    }

    private function makePgsqlDsn(array $config): string
    {
        return sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            $config['host'] ?? '127.0.0.1',
            (int) ($config['port'] ?? 5432),
            $config['database'] ?? ''
        );
    }

    private function makeMysqlDsn(array $config): string
    {
        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'] ?? '127.0.0.1',
            (int) ($config['port'] ?? 3306),
            $config['database'] ?? '',
            $config['charset'] ?? 'utf8mb4'
        );
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
}
