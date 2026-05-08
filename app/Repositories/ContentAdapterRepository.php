<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Logger;
use App\Database\QueryExecutor;
use Throwable;

final class ContentAdapterRepository extends BaseRepository
{
    private ?array $platformCompanySettingsPayload = null;

    public function __construct(
        QueryExecutor $queryExecutor,
        private readonly Logger $logger
    ) {
        parent::__construct($queryExecutor);
    }

    public function fetchBanners(): array
    {
        $platformBanners = $this->fetchPlatformSiteBanners();

        if ($platformBanners !== []) {
            return $platformBanners;
        }

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
        $homePageSettings = $this->fetchPlatformHomePageSettings();
        $featuredCategoryBanners = $homePageSettings['featuredCategoryBanners'] ?? [];

        if (is_array($featuredCategoryBanners) && $featuredCategoryBanners !== []) {
            return array_values(array_filter(array_map(
                static function (array $banner, int $index): array {
                    $imageUrl = trim((string) ($banner['imageUrl'] ?? ''));

                    if ($imageUrl === '') {
                        return [];
                    }

                    return [
                        'id' => 'featured-category-' . ($index + 1),
                        'name' => (string) ($banner['title'] ?? 'Categoria em destaque'),
                        'slug' => '',
                        'image_url' => $imageUrl,
                        'link' => (string) ($banner['ctaUrl'] ?? '/catalogo'),
                    ];
                },
                $featuredCategoryBanners,
                array_keys($featuredCategoryBanners)
            )));
        }

        return (array) config('site.homepage.featured_categories', []);
    }

    public function fetchFacilitiesBanner(): array
    {
        $homePageSettings = $this->fetchPlatformHomePageSettings();
        $items = $homePageSettings['specialConditions'] ?? [];

        if (is_array($items) && $items !== []) {
            return [
                'items' => array_values(array_filter(array_map(
                    static function (array $item): array {
                        $title = trim((string) ($item['title'] ?? ''));

                        if ($title === '') {
                            return [];
                        }

                        return [
                            'label' => $title,
                            'description' => (string) ($item['text'] ?? ''),
                            'icon_url' => (string) ($item['iconUrl'] ?? ''),
                        ];
                    },
                    $items
                ))),
            ];
        }

        return (array) config('site.homepage.facilities_banner', []);
    }

    public function fetchBlogBanner(): array
    {
        $homePageSettings = $this->fetchPlatformHomePageSettings();

        if ($homePageSettings !== []) {
            return [
                'eyebrow' => (string) ($homePageSettings['inspirationTitle'] ?? 'Conteúdo e inspiração'),
                'title' => (string) ($homePageSettings['inspirationSubtitle'] ?? ''),
                'text' => (string) ($homePageSettings['inspirationText'] ?? ''),
                'link' => (string) ($homePageSettings['inspirationUrl'] ?? '/faq'),
            ];
        }

        return (array) config('site.homepage.blog_banner', []);
    }

    public function fetchClientLogos(): array
    {
        $homePageSettings = $this->fetchPlatformHomePageSettings();
        $logos = $homePageSettings['companyLogos'] ?? [];

        if (is_array($logos) && $logos !== []) {
            return array_values(array_filter(array_map(
                static function (array $logo, int $index): array {
                    $imageUrl = trim((string) ($logo['imageUrl'] ?? ''));

                    if ($imageUrl === '') {
                        return [];
                    }

                    return [
                        'name' => (string) ($logo['title'] ?? 'Cliente ' . ($index + 1)),
                        'image_url' => $imageUrl,
                        'link' => (string) ($logo['ctaUrl'] ?? ''),
                    ];
                },
                $logos,
                array_keys($logos)
            )));
        }

        return (array) config('site.homepage.clients', []);
    }

    public function fetchSeoContent(): array
    {
        $homePageSettings = $this->fetchPlatformHomePageSettings();

        if ($homePageSettings !== []) {
            return [
                'eyebrow' => (string) ($homePageSettings['finalSeoTitle'] ?? 'Projetos corporativos'),
                'title' => (string) ($homePageSettings['finalSeoSubtitle'] ?? 'BRINDES PERSONALIZADOS'),
                'body' => (string) ($homePageSettings['finalSeoText'] ?? ''),
            ];
        }

        return (array) config('site.homepage.seo_content', []);
    }

    public function fetchHomePageSettings(): array
    {
        return $this->fetchPlatformHomePageSettings();
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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchPlatformSiteBanners(): array
    {
        try {
            $payload = $this->fetchPlatformCompanySettingsPayload();

            if (!is_array($payload) || !isset($payload['siteBanners']) || !is_array($payload['siteBanners'])) {
                return [];
            }

            return array_values(array_filter(array_map(
                static function (array $banner, int $index): array {
                    $imageUrl = trim((string) ($banner['imageUrl'] ?? ''));

                    if ($imageUrl === '') {
                        return [];
                    }

                    return [
                        'title' => (string) ($banner['title'] ?? 'Banner ' . ($index + 1)),
                        'description' => (string) ($banner['description'] ?? ''),
                        'image_url' => $imageUrl,
                        'cta_label' => (string) ($banner['ctaLabel'] ?? 'Ver detalhes'),
                        'link' => (string) ($banner['ctaUrl'] ?? '/catalogo'),
                    ];
                },
                $payload['siteBanners'],
                array_keys($payload['siteBanners'])
            )));
        } catch (Throwable $exception) {
            $this->logger->warning('Platform banners fallback failed.', ['message' => $exception->getMessage()]);

            return [];
        }
    }

    private function fetchPlatformHomePageSettings(): array
    {
        try {
            $payload = $this->fetchPlatformCompanySettingsPayload();
            $settings = is_array($payload) ? ($payload['homePageSettings'] ?? []) : [];

            return is_array($settings) ? $settings : [];
        } catch (Throwable $exception) {
            $this->logger->warning('Platform homepage settings fallback failed.', ['message' => $exception->getMessage()]);

            return [];
        }
    }

    private function fetchPlatformCompanySettingsPayload(): array
    {
        if ($this->platformCompanySettingsPayload !== null) {
            return $this->platformCompanySettingsPayload;
        }

        $baseUrl = rtrim((string) config('app.erp_base_url', ''), '/');

        if ($baseUrl === '') {
            $this->platformCompanySettingsPayload = [];
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
            $response = @file_get_contents($baseUrl . '/company-settings/public', false, $context);

            if ($response === false) {
                $this->platformCompanySettingsPayload = [];
                return [];
            }

            $payload = json_decode($response, true);
            $this->platformCompanySettingsPayload = is_array($payload) ? $payload : [];

            return $this->platformCompanySettingsPayload;
        } catch (Throwable $exception) {
            $this->logger->warning('Platform company settings fallback failed.', ['message' => $exception->getMessage()]);
            $this->platformCompanySettingsPayload = [];

            return [];
        }
    }
}
