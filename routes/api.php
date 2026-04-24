<?php

declare(strict_types=1);

use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\CatalogController;
use App\Controllers\Admin\ContentController;
use App\Controllers\HealthController;
use App\Controllers\SiteCatalogController;
use App\Controllers\SiteContentController;
use App\Middleware\AbilityMiddleware;
use App\Middleware\AuthTokenMiddleware;
use App\Middleware\JsonOnlyMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\RequestValidationMiddleware;

$publicMiddleware = [
    RequestValidationMiddleware::class,
    new RateLimitMiddleware('public'),
];

$protectedReadMiddleware = [
    RequestValidationMiddleware::class,
    new RateLimitMiddleware('protected'),
    AuthTokenMiddleware::class,
    new AbilityMiddleware('admin.read'),
];

$protectedContentWriteMiddleware = [
    RequestValidationMiddleware::class,
    JsonOnlyMiddleware::class,
    new RateLimitMiddleware('protected'),
    AuthTokenMiddleware::class,
    new AbilityMiddleware('content.write'),
];

$protectedCatalogWriteMiddleware = [
    RequestValidationMiddleware::class,
    JsonOnlyMiddleware::class,
    new RateLimitMiddleware('protected'),
    AuthTokenMiddleware::class,
    new AbilityMiddleware('catalog.write'),
];

$router->get('/health', [HealthController::class, 'check'], $publicMiddleware);
$router->get('/', [HealthController::class, 'check'], $publicMiddleware);
$router->post('/health', [HealthController::class, 'check'], [
    RequestValidationMiddleware::class,
    JsonOnlyMiddleware::class,
    new RateLimitMiddleware('public'),
]);

$router->get('/api/v1/site/settings', [SiteContentController::class, 'settings'], $publicMiddleware);
$router->get('/api/v1/site/navigation', [SiteContentController::class, 'navigation'], $publicMiddleware);
$router->get('/api/v1/categories/main', [SiteCatalogController::class, 'mainCategories'], $publicMiddleware);
$router->get('/api/v1/categories', [SiteCatalogController::class, 'allCategories'], $publicMiddleware);
$router->get('/api/v1/homepage', [SiteContentController::class, 'homepage'], $publicMiddleware);
$router->get('/api/v1/products', [SiteCatalogController::class, 'products'], $publicMiddleware);
$router->get('/api/v1/products/promotions', [SiteCatalogController::class, 'promotions'], $publicMiddleware);
$router->get('/api/v1/products/launches', [SiteCatalogController::class, 'launches'], $publicMiddleware);
$router->get('/api/v1/products/search', [SiteCatalogController::class, 'search'], $publicMiddleware);
$router->get('/api/v1/products/{idOrSlug}', [SiteCatalogController::class, 'show'], $publicMiddleware);
$router->get('/api/v1/pages/about', [SiteContentController::class, 'about'], $publicMiddleware);
$router->get('/api/v1/pages/faq', [SiteContentController::class, 'faq'], $publicMiddleware);
$router->get('/api/v1/pages/contact', [SiteContentController::class, 'contact'], $publicMiddleware);

$router->get('/api/v1/admin/auth/me', [AuthController::class, 'me'], $protectedReadMiddleware);
$router->get('/api/v1/admin/banners', [ContentController::class, 'banners'], $protectedReadMiddleware);
$router->patch('/api/v1/admin/banners', [ContentController::class, 'update'], $protectedContentWriteMiddleware);
$router->put('/api/v1/admin/banners', [ContentController::class, 'update'], $protectedContentWriteMiddleware);
$router->get('/api/v1/admin/site-texts', [ContentController::class, 'siteTexts'], $protectedReadMiddleware);
$router->patch('/api/v1/admin/site-texts', [ContentController::class, 'update'], $protectedContentWriteMiddleware);
$router->put('/api/v1/admin/site-texts', [ContentController::class, 'update'], $protectedContentWriteMiddleware);
$router->get('/api/v1/admin/categories', [CatalogController::class, 'categories'], $protectedReadMiddleware);
$router->patch('/api/v1/admin/categories', [CatalogController::class, 'update'], $protectedCatalogWriteMiddleware);
$router->put('/api/v1/admin/categories', [CatalogController::class, 'update'], $protectedCatalogWriteMiddleware);
$router->get('/api/v1/admin/products', [CatalogController::class, 'products'], $protectedReadMiddleware);
$router->get('/api/v1/admin/products/{idOrSlug}', [CatalogController::class, 'product'], $protectedReadMiddleware);
$router->patch('/api/v1/admin/products/{idOrSlug}', [CatalogController::class, 'update'], $protectedCatalogWriteMiddleware);
$router->put('/api/v1/admin/products/{idOrSlug}', [CatalogController::class, 'update'], $protectedCatalogWriteMiddleware);
