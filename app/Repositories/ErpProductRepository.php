<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;

final class ErpProductRepository extends BaseRepository
{
    /**
     * @return array<int, Product>
     */
    public function allActiveForSite(): array
    {
        $productsTable = (string) config('integrations.erp.products_table', 'Product');
        $categoriesTable = (string) config('integrations.erp.categories_table', 'Category');

        $rows = $this->queryExecutor->fetchAll(
            $this->erpConnection(),
            sprintf(
                'SELECT
                    p."id",
                    p."name",
                    p."slug",
                    p."sku",
                    p."type",
                    p."status",
                    p."shortDescription",
                    p."technicalDetails",
                    p."minimumQuantity",
                    p."leadTimeDays",
                    p."categoryId",
                    c."name" AS "categoryName"
                 FROM "%s" p
                 INNER JOIN "%s" c ON c."id" = p."categoryId"
                 WHERE p."status" = :status
                 ORDER BY p."name" ASC',
                $productsTable,
                $categoriesTable
            ),
            ['status' => 'ACTIVE']
        );

        return array_map(static fn (array $row): Product => Product::fromArray($row), $rows);
    }

    /**
     * @return array{items: array<int, Product>, total: int}
     */
    public function paginatedForSite(int $page = 1, int $perPage = 12, ?string $category = null, ?string $search = null): array
    {
        $page = max($page, 1);
        $perPage = max(min($perPage, 60), 1);
        $offset = ($page - 1) * $perPage;
        $productsTable = (string) config('integrations.erp.products_table', 'Product');
        $categoriesTable = (string) config('integrations.erp.categories_table', 'Category');

        $filters = ['p."status" = :status'];
        $params = ['status' => 'ACTIVE'];

        if ($category !== null && $category !== '') {
            $filters[] = '(CAST(c."id" AS TEXT) = :category OR c."slug" = :category)';
            $params['category'] = $category;
        }

        if ($search !== null && $search !== '') {
            $filters[] = '(p."name" ILIKE :search OR p."shortDescription" ILIKE :search OR p."sku" ILIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $where = implode(' AND ', $filters);

        $countRow = $this->queryExecutor->fetchOne(
            $this->erpConnection(),
            sprintf(
                'SELECT COUNT(*) AS "total"
                 FROM "%s" p
                 INNER JOIN "%s" c ON c."id" = p."categoryId"
                 WHERE %s',
                $productsTable,
                $categoriesTable,
                $where
            ),
            $params
        );

        $rows = $this->queryExecutor->fetchAll(
            $this->erpConnection(),
            sprintf(
                'SELECT
                    p."id",
                    p."name",
                    p."slug",
                    p."sku",
                    p."type",
                    p."status",
                    p."shortDescription",
                    p."technicalDetails",
                    p."minimumQuantity",
                    p."leadTimeDays",
                    p."categoryId",
                    c."name" AS "categoryName"
                 FROM "%s" p
                 INNER JOIN "%s" c ON c."id" = p."categoryId"
                 WHERE %s
                 ORDER BY p."name" ASC
                 LIMIT %d OFFSET %d',
                $productsTable,
                $categoriesTable,
                $where,
                $perPage,
                $offset
            ),
            $params
        );

        return [
            'items' => array_map(static fn (array $row): Product => Product::fromArray($row), $rows),
            'total' => (int) ($countRow['total'] ?? 0),
        ];
    }

    public function findByIdOrSlug(string $identifier): ?Product
    {
        $productsTable = (string) config('integrations.erp.products_table', 'Product');
        $categoriesTable = (string) config('integrations.erp.categories_table', 'Category');

        $row = $this->queryExecutor->fetchOne(
            $this->erpConnection(),
            sprintf(
                'SELECT
                    p."id",
                    p."name",
                    p."slug",
                    p."sku",
                    p."type",
                    p."status",
                    p."shortDescription",
                    p."technicalDetails",
                    p."minimumQuantity",
                    p."leadTimeDays",
                    p."categoryId",
                    c."name" AS "categoryName"
                 FROM "%s" p
                 INNER JOIN "%s" c ON c."id" = p."categoryId"
                 WHERE CAST(p."id" AS TEXT) = :identifier OR p."slug" = :identifier
                 LIMIT 1',
                $productsTable,
                $categoriesTable
            ),
            ['identifier' => $identifier]
        );

        return $row === null ? null : Product::fromArray($row);
    }
}
