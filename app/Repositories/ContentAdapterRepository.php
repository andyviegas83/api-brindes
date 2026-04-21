<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Logger;
use App\Database\QueryExecutor;
use Throwable;

final class ContentAdapterRepository extends BaseRepository
{
    public function __construct(
        QueryExecutor $queryExecutor,
        private readonly Logger $logger
    ) {
        parent::__construct($queryExecutor);
    }

    public function fetchBanners(): array
    {
        return $this->runConfiguredQuery('integrations.erp.banners_sql', 'banners', (array) config('site.homepage.main_banners', []));
    }

    public function fetchTestimonials(): array
    {
        return $this->runConfiguredQuery('integrations.erp.testimonials_sql', 'testimonials', (array) config('site.testimonials', []));
    }

    public function fetchPromotions(): array
    {
        return $this->runConfiguredQuery('integrations.erp.promotions_sql', 'promotions', []);
    }

    public function fetchReleases(): array
    {
        return $this->runConfiguredQuery('integrations.erp.releases_sql', 'releases', []);
    }

    public function fetchInstitutionalTexts(): array
    {
        return $this->runConfiguredQuery('integrations.erp.institutional_texts_sql', 'institutional_texts', (array) config('site.institutional_texts', []));
    }

    public function fetchFeaturedCategories(): array
    {
        return (array) config('site.homepage.featured_categories', []);
    }

    public function fetchFacilitiesBanner(): array
    {
        return (array) config('site.homepage.facilities_banner', []);
    }

    public function fetchBlogBanner(): array
    {
        return (array) config('site.homepage.blog_banner', []);
    }

    public function fetchClientLogos(): array
    {
        return (array) config('site.homepage.clients', []);
    }

    public function fetchSeoContent(): array
    {
        return (array) config('site.homepage.seo_content', []);
    }

    public function fetchPage(string $pageKey): array
    {
        return (array) config(sprintf('site.pages.%s', $pageKey), []);
    }

    public function fetchNavigation(): array
    {
        return (array) config('site.navigation', []);
    }

    private function runConfiguredQuery(string $configKey, string $dataset, array $fallback = []): array
    {
        $sql = trim((string) config($configKey, ''));

        if ($sql === '') {
            return $fallback;
        }

        try {
            return $this->queryExecutor->fetchAll($this->erpConnection(), $sql);
        } catch (Throwable $exception) {
            $this->logger->warning('ERP adapter query failed', [
                'dataset' => $dataset,
                'message' => $exception->getMessage(),
            ]);

            return $fallback;
        }
    }
}
