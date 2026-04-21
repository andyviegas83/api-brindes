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
            return $this->categorySerializer->collection($this->categoryRepository->mainMenu($limit));
        } catch (Throwable $exception) {
            $this->logger->warning('Main categories fallback activated.', ['message' => $exception->getMessage()]);

            return [];
        }
    }

    public function allCategories(): array
    {
        try {
            return $this->categorySerializer->collection($this->categoryRepository->all());
        } catch (Throwable $exception) {
            $this->logger->warning('Categories fallback activated.', ['message' => $exception->getMessage()]);

            return [];
        }
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
        $items = $this->contentAdapterRepository->fetchPromotions();

        return $this->paginateCollection($items, $page, $perPage);
    }

    public function launches(int $page = 1, int $perPage = 12): array
    {
        $items = $this->contentAdapterRepository->fetchReleases();

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
}
