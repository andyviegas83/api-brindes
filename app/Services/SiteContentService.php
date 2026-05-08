<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Repositories\ContentAdapterRepository;
use App\Repositories\ErpCompanySettingsRepository;
use App\Serializers\CompanyProfileSerializer;
use Throwable;

final class SiteContentService
{
    public function __construct(
        private readonly ErpCompanySettingsRepository $companySettingsRepository,
        private readonly SiteCatalogService $catalogService,
        private readonly ContentAdapterRepository $contentAdapterRepository,
        private readonly CompanyProfileSerializer $companyProfileSerializer,
        private readonly Logger $logger
    ) {
    }

    public function siteSettings(): array
    {
        try {
            $company = $this->companyProfileSerializer->serialize($this->companySettingsRepository->findDefault());
        } catch (Throwable $exception) {
            $this->logger->warning('Company settings fallback activated.', ['message' => $exception->getMessage()]);
            $company = null;
        }

        return [
            'company' => $company,
            'texts' => $this->contentAdapterRepository->fetchInstitutionalTexts(),
        ];
    }

    public function navigation(): array
    {
        $navigation = $this->contentAdapterRepository->fetchNavigation();

        return [
            'institutional_links' => $navigation['institutional_links'] ?? [],
            'main_categories' => $this->catalogService->mainCategories(),
            'all_categories' => $this->catalogService->allCategories(),
            'all_categories_path' => '/categorias',
        ];
    }

    public function homepage(): array
    {
        $settings = $this->contentAdapterRepository->fetchHomePageSettings();

        return [
            'main_banners' => $this->contentAdapterRepository->fetchBanners(),
            'settings' => $this->withoutDuplicatedHomepageMedia($settings),
            'featured_categories' => $this->contentAdapterRepository->fetchFeaturedCategories(),
            'facilities_banner' => $this->contentAdapterRepository->fetchFacilitiesBanner(),
            'testimonials' => $this->contentAdapterRepository->fetchTestimonials(),
            'blog_banner' => $this->contentAdapterRepository->fetchBlogBanner(),
            'client_logos' => $this->contentAdapterRepository->fetchClientLogos(),
            'seo_content' => $this->contentAdapterRepository->fetchSeoContent(),
        ];
    }

    private function withoutDuplicatedHomepageMedia(array $settings): array
    {
        unset(
            $settings['featuredCategoryBanners'],
            $settings['companyLogos'],
            $settings['siteBanners']
        );

        return $settings;
    }

    public function page(string $pageKey): array
    {
        return $this->contentAdapterRepository->fetchPage($pageKey);
    }
}
