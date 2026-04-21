<?php

declare(strict_types=1);

namespace App\Serializers;

final class PaginationSerializer
{
    public function serialize(int $page, int $perPage, int $total): array
    {
        $lastPage = max((int) ceil($total / max($perPage, 1)), 1);

        return [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
        ];
    }
}
