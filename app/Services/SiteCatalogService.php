<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Repositories\ContentAdapterRepository;
use App\Repositories\ErpCategoryRepository;
use App\Repositories\ErpProductRepository;
use App\Serializers\CategorySerializer;
use App\Serializers\PaginationSerializer;
use App\Serializers\ProductSerializer;
use Throwable;

final class SiteCatalogService
{
    public function __construct(
        private readonly ErpCategoryRepository $categoryRepository,
        private readonly ErpProductRepository $productRepository,
        private readonly ContentAdapterRepository $contentAdapterRepository,
        private readonly CategorySerializer $categorySerializer,
        private readonly ProductSerializer $productSerializer,
        private readonly PaginationSerializer $paginationSerializer,
        private readonly Logger $logger
    ) {
    }

    public function mainCategories(int $limit = 10): array
    {
        try {
            $categories = $this->categorySerializer->collection($this->categoryRepository->mainMenu($limit));

            if ($categories !== []) {
                return $categories;
            }
        } catch (Throwable $exception) {
            $this->logger->warning('Main categories fallback activated.', ['message' => $exception->getMessage()]);
        }

        return array_slice($this->platformNavigationCategories('topMenuCategories'), 0, $limit);
    }

    public function allCategories(): array
    {
        try {
            $categories = $this->categorySerializer->collection($this->categoryRepository->sideMenu());

            if ($categories !== []) {
                return $categories;
            }
        } catch (Throwable $exception) {
            $this->logger->warning('Categories fallback activated.', ['message' => $exception->getMessage()]);
        }

        return $this->platformNavigationCategories('sideMenuCategories');
    }

    public function products(int $page = 1, int $perPage = 12, ?string $category = null, ?string $search = null): array
    {
        try {
            $result = $this->productRepository->paginatedForSite($page, $perPage, $category, $search);
        } catch (Throwable $exception) {
            $this->logger->warning('Products fallback activated.', ['message' => $exception->getMessage()]);

            return [
                'items' => [],
                'pagination' => $this->paginationSerializer->serialize($page, $perPage, 0),
            ];
        }

        return [
            'items' => $this->productSerializer->collection($result['items']),
            'pagination' => $this->paginationSerializer->serialize($page, $perPage, (int) $result['total']),
        ];
    }

    public function promotions(int $page = 1, int $perPage = 12): array
    {
        try {
            $items = array_values(array_filter(
                $this->productSerializer->collection($this->productRepository->allActiveForSite()),
                static fn (array $product): bool => (bool) ($product['is_promotion'] ?? false)
            ));
        } catch (Throwable) {
            $items = $this->contentAdapterRepository->fetchPromotions();
        }

        return $this->paginateCollection($items, $page, $perPage);
    }

    public function launches(int $page = 1, int $perPage = 12): array
    {
        try {
            $items = array_values(array_filter(
                $this->productSerializer->collection($this->productRepository->allActiveForSite()),
                static fn (array $product): bool => (bool) ($product['is_launch'] ?? false)
            ));
        } catch (Throwable) {
            $items = $this->contentAdapterRepository->fetchReleases();
        }

        return $this->paginateCollection($items, $page, $perPage);
    }

    public function findProduct(string $identifier): ?array
    {
        try {
            $product = $this->productRepository->findByIdOrSlug($identifier);
        } catch (Throwable $exception) {
            $this->logger->warning('Product lookup fallback activated.', [
                'identifier' => $identifier,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        return $product === null ? null : $this->productSerializer->serialize($product);
    }

    private function paginateCollection(array $items, int $page, int $perPage): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($items, $offset, $perPage);

        return [
            'items' => array_values($slice),
            'pagination' => $this->paginationSerializer->serialize($page, $perPage, count($items)),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function platformNavigationCategories(string $key): array
    {
        $baseUrl = rtrim((string) config('app.erp_base_url', ''), '/');

        if ($baseUrl === '') {
            return [];
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "Accept: application/json\r\n",
                    'timeout' => 5,
                    'ignore_errors' => true,
                ],
            ]);
            $response = @file_get_contents($baseUrl . '/categories/public/site-navigation', false, $context);

            if ($response === false) {
                return [];
            }

            $payload = json_decode($response, true);

            if (!is_array($payload) || !isset($payload[$key]) || !is_array($payload[$key])) {
                return [];
            }

            return array_values(array_map([$this, 'normalizePlatformCategory'], $payload[$key]));
        } catch (Throwable $exception) {
            $this->logger->warning('Platform category navigation fallback failed.', ['message' => $exception->getMessage()]);

            return [];
        }
    }

    /**
     * @param array<string, mixed> $category
     * @return array<string, mixed>
     */
    private function normalizePlatformCategory(array $category): array
    {
        return [
            'id' => $category['id'] ?? null,
            'slug' => (string) ($category['slug'] ?? ''),
            'name' => (string) ($category['name'] ?? ''),
            'short_description' => (string) ($category['description'] ?? ''),
            'icon' => 'gift',
            'icon_url' => (string) ($category['iconUrl'] ?? ''),
            'parent_id' => $category['parentId'] ?? null,
            'show_in_side_menu' => false,
            'show_in_top_menu' => false,
            'seo_title' => null,
            'seo_description' => null,
        ];
    }
}
