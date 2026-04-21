<?php

declare(strict_types=1);

namespace App\Database;

final class QueryExecutor
{
    public function __construct(
        private readonly DatabaseManager $databaseManager
    ) {
    }

    public function fetchOne(string $connection, string $sql, array $params = []): ?array
    {
        $statement = $this->databaseManager->connection($connection)->prepare($sql);
        $statement->execute($params);

        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    public function fetchAll(string $connection, string $sql, array $params = []): array
    {
        $statement = $this->databaseManager->connection($connection)->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }
}
