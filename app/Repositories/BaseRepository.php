<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\QueryExecutor;

abstract class BaseRepository
{
    public function __construct(
        protected readonly QueryExecutor $queryExecutor
    ) {
    }

    protected function erpConnection(): string
    {
        return (string) config('integrations.erp.connection', 'erp');
    }
}
