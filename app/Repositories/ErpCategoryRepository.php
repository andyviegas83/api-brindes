<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;

final class ErpCategoryRepository extends BaseRepository
{
    /**
     * @return array<int, Category>
     */
    public function allVisibleForSite(): array
    {
        $table = (string) config('integrations.erp.categories_table', 'Category');

        $rows = $this->queryExecutor->fetchAll(
            $this->erpConnection(),
            sprintf(
                'SELECT "id", "name", "slug", "description", "parentId", "showInSideMenu", "showInTopMenu", "iconUrl", "seoTitle", "seoDescription"
                 FROM "%s"
                 WHERE "showInSideMenu" = true OR "showInTopMenu" = true
                 ORDER BY "name" ASC',
                $table
            )
        );

        return array_map(static fn (array $row): Category => Category::fromArray($row), $rows);
    }

    /**
     * @return array<int, Category>
     */
    public function sideMenu(): array
    {
        $table = (string) config('integrations.erp.categories_table', 'Category');

        $rows = $this->queryExecutor->fetchAll(
            $this->erpConnection(),
            sprintf(
                'SELECT "id", "name", "slug", "description", "parentId", "showInSideMenu", "showInTopMenu", "iconUrl", "seoTitle", "seoDescription"
                 FROM "%s"
                 WHERE "showInSideMenu" = true
                 ORDER BY "name" ASC',
                $table
            )
        );

        return array_map(static fn (array $row): Category => Category::fromArray($row), $rows);
    }

    /**
     * @return array<int, Category>
     */
    public function mainMenu(int $limit = 10): array
    {
        $table = (string) config('integrations.erp.categories_table', 'Category');

        $rows = $this->queryExecutor->fetchAll(
            $this->erpConnection(),
            sprintf(
                'SELECT "id", "name", "slug", "description", "parentId", "showInSideMenu", "showInTopMenu", "iconUrl", "seoTitle", "seoDescription"
                 FROM "%s"
                 WHERE "showInTopMenu" = true
                 ORDER BY "name" ASC
                 LIMIT %d',
                $table,
                max($limit, 1)
            )
        );

        return array_map(static fn (array $row): Category => Category::fromArray($row), $rows);
    }

    /**
     * @return array<int, Category>
     */
    public function all(): array
    {
        $table = (string) config('integrations.erp.categories_table', 'Category');

        $rows = $this->queryExecutor->fetchAll(
            $this->erpConnection(),
            sprintf(
                'SELECT "id", "name", "slug", "description", "parentId", "showInSideMenu", "showInTopMenu", "iconUrl", "seoTitle", "seoDescription"
                 FROM "%s"
                 ORDER BY "name" ASC',
                $table
            )
        );

        return array_map(static fn (array $row): Category => Category::fromArray($row), $rows);
    }

    public function findByIdOrSlug(string $identifier): ?Category
    {
        $table = (string) config('integrations.erp.categories_table', 'Category');

        $row = $this->queryExecutor->fetchOne(
            $this->erpConnection(),
            sprintf(
                'SELECT "id", "name", "slug", "description", "parentId", "showInSideMenu", "showInTopMenu", "iconUrl", "seoTitle", "seoDescription"
                 FROM "%s"
                 WHERE CAST("id" AS TEXT) = :identifier OR "slug" = :identifier
                 LIMIT 1',
                $table
            ),
            ['identifier' => $identifier]
        );

        return $row === null ? null : Category::fromArray($row);
    }
}
